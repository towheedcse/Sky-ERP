<?php

class BackupSystem
{

    private $pdo; // Database connection
    private $config;
    private $logFile = 'backup_system.log';

    public function __construct($host, $user, $pass, $dbname, $config = [])
    {
        // Connect to Database for Job Management
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }

        $this->config = $config;
    }

    /**
     * The Main Dispatcher
     * Finds jobs that are due and executes them
     */
    public function run()
    {

        $this->cleanupLogs();

        // Find a job that is pending or failed (and ready for retry)
        // AND is scheduled to run now (or in the past)
        $sql = "SELECT * FROM backup_jobs 
                WHERE (status = 'pending' OR status = 'failed') 
                AND next_run <= NOW() 
                LIMIT 1";

        $stmt = $this->pdo->query($sql);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$job) {
            //$this->log("No pending jobs found.");
            return;
        }

        $this->log("Starting Backup Dispatcher...");

        // 1. Lock the Job (Mark as running)
        $this->updateJobStatus($job['id'], 'running');
        $this->log("Processing Job ID: " . $job['id']);

	// Initialize variable OUTSIDE so it is accessible in catch
 	$filePath = null; 

        try {
            // 2. Generate SQL File
            $filePath = $this->generateDump();

            // 3. Execute Upload with Retry Mechanism
            $this->executeWithRetry($filePath, $job);

            // 4. Success Cleanup
	    // A. Delete the file we just uploaded
    	    $this->deleteLocalFile($filePath);
    	    // B. Delete ANY remaining files (cleanup orphans from previous failed runs)
    	    $this->cleanBackupDirectory();
            
            $this->sendNotification("Backup Success", "Job ID {$job['id']} completed successfully.");

            // 5. Reschedule (e.g., 24 hours from now)
            $success_interval = isset($this->config['success_interval']) ? $this->config['success_interval'] : '+1 day';
            $fixedTime = isset($this->config['fixed_time']) ? $this->config['fixed_time'] : '23:25:00';

	    $now = time();

	    // CASE 1: Daily scheduling
	    if (stripos($success_interval, 'day') !== false) {

	        // Build today's fixed time
	        $todayFixed = date('Y-m-d') . ' ' . $fixedTime;
	        $todayFixedTs = strtotime($todayFixed);

	        if ($todayFixedTs > $now) {
	    	// Still upcoming today
	    	$nextRun = $todayFixed;
	        } else {
	    	// Tomorrow at fixed time
	    	$nextRun = date('Y-m-d', strtotime($success_interval)) . ' ' . $fixedTime;
	        }

	    } else {

	        // CASE 2: Hour/minute based interval
	        // Example: +2 hours, +30 minutes, etc.
	        $nextRun = date('Y-m-d H:i:s', strtotime($success_interval, $now));
	    }

            $this->rescheduleJob($job['id'], 'pending', $nextRun);

            $this->log("Job ID: " . $job['id'] . " finished successfully.");

        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            $this->log("Job ID: " . $job['id'] . " FAILED. Error: " . $errorMsg, "ERROR");

    	    // --- CRITICAL FIX: Clean up the failed file ---
    	    // Since generateDump creates a NEW file next time, this failed file is garbage.
       	    if ($filePath !== null && file_exists($filePath)) {
        	$this->deleteLocalFile($filePath);
        	$this->log("Deleted failed backup file: " . basename($filePath));
	    }

            // Calculate next retry time (exponential backoff or fixed)
            // e.g., retry in 10 minutes
            $failure_interval = isset($this->config['failure_interval']) ? $this->config['failure_interval'] : '+10 minutes';
            $nextRun = date('Y-m-d H:i:s', strtotime($failure_interval));

            $this->updateJobError($job['id'], 'failed', $nextRun, $errorMsg);

            // Notify Admin about Failure
            $this->sendNotification("Backup Failed", "Job ID {$job['id']} failed: $errorMsg");
        }
    }

    /**
     * Retry Logic Wrapper
     */
    private function executeWithRetry($filePath, $job)
    {
        $maxRetries = isset($this->config['max_retries']) ? $this->config['max_retries'] : 3;
        $attempt = 1;
        $lastError = "";

        while ($attempt <= $maxRetries) {
            try {
                $this->log("Upload Attempt $attempt for Job ID: " . $job['id']);

                // Try uploading
                if ($this->config['method'] == 'ftp') {
                    $this->uploadToFtp($filePath);
                } elseif ($this->config['method'] == 'dropbox') {
                    $this->uploadToDropbox($filePath);
                } elseif ($this->config['method'] == 'google') {
                    $this->uploadToGoogleDrive($filePath);
                }

                return; // Success, exit loop

            } catch (Exception $e) {
                $lastError = $e->getMessage();
                $this->log("Attempt $attempt failed: " . $lastError, "ERROR");

                if ($attempt < $maxRetries) {
                    sleep(5); // Wait 5 seconds before retrying
                }
                $attempt++;
            }
        }

        // If we get here, all retries failed
        throw new Exception("Upload failed after $maxRetries attempts. Last error: $lastError");
    }

    // --- Helper Functions (Same as before, slightly adapted) ---

    private function generateDump()
    {

        $dbHost = $this->config['db_host'];
        $dbUser = $this->config['db_user'];
        $dbName = $this->config['db_name'];
        $dbPass = $this->config['db_pass'];

        $backupDir = __DIR__ . '/backups/';
        if (!file_exists($backupDir)) mkdir($backupDir, 0777, true);

	$DB_NAME = trim($dbName);
        $date = date('Y-m-d_H-i-s');
        $filename = $DB_NAME . "_db_backup_{$date}.sql.gz";

        $filepath = $backupDir . $filename;

        // Temporary file for the raw SQL dump
        $filenameRaw = $DB_NAME . "_db_backup_{$date}.sql";
        $filepathRaw = $backupDir . $filenameRaw;

        // 1. Increase time limit for large dumps (prevent PHP timeout)
        set_time_limit(0); // Unlimited execution time

        // Force the path
        $dumpBin = '/opt/lampp/bin/mysqldump';
        $gzipBin = '/bin/gzip';

        $passArg = '';
        if (!empty($dbPass)) {
            //$passArg = "-p" . escapeshellarg($dbPass);
            $passArg = "-p'" . addslashes($dbPass) . "'";
        }
        // --- STEP 1: DUMP TO RAW SQL FILE ---
        // Flags explained:
        // --single-transaction: Prevents locking issues (crucial for live InnoDB DBs)
        // --routines: Includes Stored Procedures and Functions
        // --triggers: Includes Triggers
        // --events: Includes Events
        // --quick: Retrieves rows one at a time (prevents memory crashes on large tables)
        // --create-options: Includes engine, charset, etc.
        // --add-drop-table: Adds DROP TABLE statements (clean restore)

        $command = "{$dumpBin} -h{$dbHost} -u{$dbUser} {$passArg} " .
            "--single-transaction --routines --triggers --events --quick --create-options --add-drop-table " .
            "{$dbName} > {$filepathRaw} 2>&1";

        $this->log("Running dump command: " . $command);

        $output = [];
        $return_var = 0;

        // Execute Dump
        exec($command, $output, $return_var);

        // Check specifically for mysqldump errors
        if ($return_var !== 0) {
            // Filter out common warnings that aren't fatal, but log them if needed
            $errorText = implode("\n", $output);
            // Clean up partial file
            if (file_exists($filepathRaw)) unlink($filepathRaw);

            $this->log("mysqldump failed. Code: $return_var. Output: " . $errorText, "ERROR");
            throw new Exception("Database dump failed. Check logs for details.");
        }

        // Verify the raw SQL file was created and has content
        if (!file_exists($filepathRaw) || filesize($filepathRaw) < 100) {
            throw new Exception("SQL file created but is empty or too small.");
        }

        // --- STEP 2: COMPRESS THE RAW SQL FILE ---
        $this->log("Compressing backup...");
        $commandZip = "{$gzipBin} -f {$filepathRaw}"; // -f forces overwrite if exists

        exec($commandZip, $outputZip, $returnZip);

        if ($returnZip !== 0) {
            // Gzip failed, but we have the raw SQL! Return that instead.
            $this->log("Gzip failed, returning raw SQL file.", "WARN");
            return $filepathRaw;
        }

        // Check if gzipped file exists
        if (file_exists($filepath)) {
            $this->log("Backup successful: $filename");
            // Optional: Delete raw SQL file since we have the .gz
            if (file_exists($filepathRaw)) unlink($filepathRaw);
            return $filepath;
        } else {
            // Gzip seemed to work but file is missing? Return raw SQL.
            return $filepathRaw;
        }

    }

    private function deleteLocalFile($path)
    {
        if (file_exists($path)) {
            unlink($path);
            $this->log("Deleted local file: $path");
        }
    }


	private function cleanBackupDirectory()
	{
	    $backupDir = __DIR__ . '/backups/';
	    
	    // Check if directory exists
	    if (!is_dir($backupDir)) {
		return;
	    }

	    // Get all files in the directory
	    $files = glob($backupDir . '*');
	    
	    $count = 0;
	    foreach ($files as $file) {
		// Make sure it is a file (not a subdirectory) and delete it
		if (is_file($file)) {
		    unlink($file);
		    $count++;
		}
	    }
	    
	    if ($count > 0) {
		$this->log("Cleaned up $count old backup file(s) from directory.");
	    }
	}

    private function uploadToFtp($localFile)
    {
        // 1. Load config from Database
        try {
            $ftpConfig = $this->getDestinationConfig('ftp');
        } catch (Exception $e) {
            $this->log($e->getMessage(), "ERROR");
            return false;
        }

        // 2. Map DB Keys to Variables
        // Note: These keys must match what you inserted into the 'credentials' JSON column
        $host = $ftpConfig['host'];
        $user = $ftpConfig['username'];
        $pass = $ftpConfig['password'];

        // Default to '/' if not set in DB
        $remotePath = isset($ftpConfig['remote_path']) ? $ftpConfig['remote_path'] : '/';

        // Default to Port 21 if not set
        $port = isset($ftpConfig['port']) ? (int)$ftpConfig['port'] : 21;

        // Default to Passive mode (true) if not set
        $passiveMode = isset($ftpConfig['passive']) ? (bool)$ftpConfig['passive'] : true;

        $filename = basename($localFile);

        $this->log("Connecting to FTP server: $host:$port");

        // Connect
        $connId = ftp_connect($host, $port, 10);

        if (!$connId) {
            throw new Exception("FTP Connection failed or timed out.");
        }

        $loginResult = ftp_login($connId, $user, $pass);

        if (!$loginResult) {
            ftp_close($connId);
            throw new Exception("FTP Login failed for user: $user");
        }

        // Set Passive Mode (from DB config or default)
        ftp_pasv($connId, $passiveMode);

        // Change remote directory
        // Note: ftp_chdir returns false if path is '/' sometimes, so we check if path != '/'
        if (!empty($remotePath) && $remotePath !== '/') {
            if (!ftp_chdir($connId, $remotePath)) {
                ftp_close($connId);
                throw new Exception("FTP Failed to change directory to: $remotePath");
            }
        }

        $this->log("Uploading $filename to FTP...");

        // Upload binary mode
        $upload = ftp_put($connId, $filename, $localFile, FTP_BINARY);

        ftp_close($connId);

        if (!$upload) {
            throw new Exception("FTP Upload failed for file: $filename");
        }

        $this->log("FTP Upload successful.");
    }


    private function getDestinationConfig($provider)
    {
        $sql = "SELECT id, credentials FROM backup_destinations 
            WHERE provider = :provider AND is_active = 1 
            LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['provider' => $provider]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("No configuration found for provider: $provider");
        }

        // Decode the TEXT string into a PHP array
        $config = json_decode($row['credentials'], true);

        // Store the DB ID so we can update the correct row later
        $config['db_id'] = $row['id'];

        return $config;
    }

    private function saveDestinationConfig($provider, $configArray)
    {
        // Convert the array back to a JSON string
        $jsonString = json_encode($configArray);

        $sql = "UPDATE backup_destinations 
            SET credentials = :creds, updated_at = NOW() 
            WHERE provider = :provider";

        // Alternatively, you can use WHERE id = :id if you prefer
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'creds' => $jsonString,
            'provider' => $provider
        ]);

        $this->log("Updated configuration for $provider in database.");
    }

    private function refreshDropboxToken($currentConfig)
    {
        // 1. Extract keys from the config array (loaded from DB)
        $appKey = $currentConfig['app_key'];
        $appSecret = $currentConfig['app_secret'];
        $refreshToken = $currentConfig['refresh_token']; // Make sure your DB has 'refresh_token'

        $this->log("Refreshing Dropbox Token...");

        $ch = curl_init("https://api.dropboxapi.com/oauth2/token");

        $postFields = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $appKey,
            'client_secret' => $appSecret
        ];

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Failed to refresh Dropbox token. Response: " . $response);
        }

        $data = json_decode($response, true);
        $newAccessToken = $data['access_token'];

        // 2. Update the config array with the new token
        $currentConfig['access_token'] = $newAccessToken;

        // 3. Save the updated array back to the TEXT column in DB
        $this->saveDestinationConfig('dropbox', $currentConfig);

        $this->log("Dropbox token refreshed and saved to DB.");
        return $currentConfig;
    }

    private function uploadToDropbox($localFile)
    {
        // 1. Load fresh config from DB (Text string -> Array)
        try {
            $dropboxConfig = $this->getDestinationConfig('dropbox');
        } catch (Exception $e) {
            $this->log($e->getMessage(), "ERROR");
            return false;
        }

        // Get the token from the loaded array
        $token = $dropboxConfig['access_token'];

        // --- NEW: CHECK IF TOKEN IS EMPTY (First Run Scenario) ---
       if (empty($token)) {
           $this->log("Access token is missing. Refreshing automatically...", "WARN");
        
           // Refresh the token (This updates the DB and returns the new config)
           $dropboxConfig = $this->refreshDropboxToken($dropboxConfig);
        
           // Get the new valid token
           $token = $dropboxConfig['access_token'];
       }
       // --------------------------------------------------------

        $filename = basename($localFile);
        $fileContent = file_get_contents($localFile);

        if (strlen($fileContent) === 0) {
            throw new Exception("Dropbox Upload failed: File is empty");
        }

        $apiArgs = [
            "path" => "/" . $filename,
            "mode" => "add",
            "autorename" => true,
            "mute" => false
        ];

        $apiHeader = "Dropbox-API-Arg: " . json_encode($apiArgs);
        $this->log("Uploading to Dropbox: $filename");

        $ch = curl_init("https://content.dropboxapi.com/2/files/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $token,
            "Content-Type: application/octet-stream",
            $apiHeader
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // --- CHECK FOR EXPIRED TOKEN ---
        if ($httpCode === 401) {
            $this->log("Dropbox token expired. Refreshing...", "WARN");

            // 1. Refresh the token (updates DB and returns new config array)
            $newConfig = $this->refreshDropboxToken($dropboxConfig);

            // 2. Update the local token variable for the retry
            $newToken = $newConfig['access_token'];

            // 3. RETRY THE UPLOAD IMMEDIATELY
            $this->log("Retrying Dropbox upload with new token...");

            // We can call the function recursively, but we need to make sure
            // it uses the new token. Since we reload config at the start of the function,
            // and the DB is already updated, we can simply call it again.
            return $this->uploadToDropbox($localFile);
        }

        // --- CHECK FOR OTHER ERRORS ---
        if ($httpCode !== 200) {
            $this->log("Dropbox Error ($httpCode): " . $response, "ERROR");
            throw new Exception("Dropbox Upload Failed. Code: $httpCode.");
        }

        $this->log("Dropbox Upload successful.");
    }

    private function refreshGoogleToken($currentConfig)
    {
        $clientId = $currentConfig['client_id'];
        $clientSecret = $currentConfig['client_secret'];
        $refreshToken = $currentConfig['refresh_token'];

        $this->log("Refreshing Google Drive Token...");

        $ch = curl_init("https://oauth2.googleapis.com/token");

        $postFields = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ];

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Failed to refresh Google token. Response: " . $response);
        }

        $data = json_decode($response, true);
        $newAccessToken = $data['access_token'];

        // Update the config array
        $currentConfig['access_token'] = $newAccessToken;

        // Save to DB
        $this->saveDestinationConfig('google_drive', $currentConfig);

        $this->log("Google Drive token refreshed and saved to DB.");
        return $currentConfig;
    }

    private function uploadToGoogleDrive($localFile)
    {
        // 1. Load config from DB
        try {
            $googleConfig = $this->getDestinationConfig('google_drive');
        } catch (Exception $e) {
            $this->log($e->getMessage(), "ERROR");
            return false;
        }

        $token = $googleConfig['access_token'];

        // --- CHECK IF TOKEN IS EMPTY (First Run) ---
        if (empty($token)) {
            $this->log("Google Access token is missing. Refreshing...", "WARN");
            $googleConfig = $this->refreshGoogleToken($googleConfig);
            $token = $googleConfig['access_token'];
        }
        // -----------------------------------------

        $filename = basename($localFile);
        $fileContent = file_get_contents($localFile);

        // 1. Construct the Multipart Body manually
        $boundary = uniqid('-----------');

        // Metadata Part
        $metadata = json_encode(['name' => $filename]);
        $bodyPart1 = "--$boundary\r\n";
        $bodyPart1 .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
        $bodyPart1 .= $metadata . "\r\n";

        // Media Part
        $bodyPart2 = "--$boundary\r\n";
        $bodyPart2 .= "Content-Type: application/octet-stream\r\n\r\n";
        $bodyPart2 .= $fileContent . "\r\n";

        $bodyEnd = "--$boundary--";
        $finalBody = $bodyPart1 . $bodyPart2 . $bodyEnd;

        $this->log("Uploading to Google Drive: $filename");

        $ch = curl_init("https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart");

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $finalBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $token,
            "Content-Type: multipart/related; boundary=$boundary",
            "Content-Length: " . strlen($finalBody)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // --- CHECK FOR EXPIRED TOKEN ---
        if ($httpCode === 401) {
            $this->log("Google token expired. Refreshing...", "WARN");
            $newConfig = $this->refreshGoogleToken($googleConfig);
            // Retry
            return $this->uploadToGoogleDrive($localFile);
        }

        if ($httpCode !== 200 && $httpCode !== 201) {
            $this->log("Google Drive Error ($httpCode): " . $response, "ERROR");
            throw new Exception("Google Drive Upload Failed. Code: $httpCode. Curl: $curlError");
        }

        $this->log("Google Drive Upload successful.");
    }


    // --- Admin & Logging Functions ---

    private function sendNotification($subject, $body)
    {
        $adminEmail = isset($this->config['admin_email']) ? $this->config['admin_email'] : 'admin@example.com';
        $PurchaseOrder = new PurchaseOrder();
        $PurchaseOrder->sendDBBackupMail($adminEmail, $subject, $body);
    }


    private function log($message, $level = 'INFO')
    {

        // --- 1. Log to File (Keep existing logic) ---
        $entry = date("Y-m-d H:i:s") . " [$level] - " . $message . "\n";
        file_put_contents($this->logFile, $entry, FILE_APPEND);

        // --- 2. Log to Database (New Logic) ---
        // We wrap this in a try/catch so if the DB is down,
        // we don't crash the script trying to write logs about the DB being down.
        try {
            // Use the PDO connection ($this->pdo) established in the constructor
            $stmt = $this->pdo->prepare("INSERT INTO backup_logs (level, message) VALUES (?, ?)");
            $stmt->execute([$level, $message]);
        } catch (PDOException $e) {
            // If DB log fails, just append the error to the text file so we know.
            file_put_contents($this->logFile, "DB Log Error: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }

    // --- Database Job Management Functions ---

    private function updateJobStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE backup_jobs SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    private function updateJobError($id, $status, $nextRun, $error)
    {
        $stmt = $this->pdo->prepare("UPDATE backup_jobs SET status = ?, next_run = ?, retry_count = retry_count + 1, last_error = ? WHERE id = ?");
        $stmt->execute([$status, $nextRun, $error, $id]);
    }

    private function rescheduleJob($id, $status, $nextRun)
    {
        $stmt = $this->pdo->prepare("UPDATE backup_jobs SET status = ?, next_run = ?, retry_count = 0, last_error = NULL WHERE id = ?");
        $stmt->execute([$status, $nextRun, $id]);
    }

    private function cleanupLogs()
    {
        // 1. Delete rows older than 30 days
        $sql = "DELETE FROM backup_logs WHERE created_at < NOW() - INTERVAL 30 DAY";

        $stmt = $this->pdo->exec($sql);

        if ($stmt > 0) {
            $this->log("Cleaned up old logs. Affected rows: $stmt");
        }

        // --- OPTIONAL: IF YOU REALLY WANT TO RESET ID (Read warning above) ---
        // Only do this if the table is now empty, or if you absolutely insist.
        /*
        $this->pdo->exec("ALTER TABLE backup_logs AUTO_INCREMENT = 1");
        */
    }
}

?>
