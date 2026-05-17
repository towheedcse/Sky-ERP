<?php
ini_set("display_errors","On");
ob_start();

$projectFolder = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'))[0];
define('PROJECT_NAME', $projectFolder);
define('PROJECT_DIR', '/'. PROJECT_NAME);

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/configs/common/database.conf.php';
require_once ROOT_PATH . '/classes/purchase_order.class.php';
require_once __DIR__ . '/BackupSystem.php';

//Store DB Formate
//{
//    "app_key":"YOUR_DROPBOX_APP_KEY",
//    "app_secret":"YOUR_DROPBOX_APP_SECRET",
//    "refresh_token":"YOUR_DROPBOX_REFRESH_TOKEN",
//    "access_token":"YOUR_INITIAL_ACCESS_TOKEN"
//}
//{
//    "client_id":"YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com",
//    "client_secret":"YOUR_GOOGLE_CLIENT_SECRET",
//    "refresh_token":"YOUR_GOOGLE_REFRESH_TOKEN",
//    "access_token":"YOUR_INITIAL_ACCESS_TOKEN"
//}
//{
//    "host":"ftp.yourwebsite.com",
//    "port":21,
//    "username":"your_ftp_user",
//    "password":"your_ftp_password",
//    "passive":true,
//    "remote_path":"/backups"
//}


// 1. CONFIGURATION
 $config = [
     // Database Credentials for backing up data
     'db_host' => DB_HOST,
     'db_user' => DB_USER,
     'db_pass' => DB_PASS,
     'db_name' => DB_NAME,

     // Upload Method: 'ftp', 'dropbox', or 'google'
     'method' => 'dropbox',

     // Admin Email
     'admin_email' => 'imransabbu@gmail.com',

     // System Settings
     'max_retries' => 3, // Try 3 times if error occurs
	
     // backup success interval
     'success_interval' => '+1 day',
     'fixed_time' => '23:25:00',
	
     // backup failure interval
     'failure_interval' => '+8 minutes',
 ];

// 2. RUN THE SYSTEM
try {
    // Pass credentials to connect to the DB where the job table lives
    $system = new BackupSystem($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name'], $config);
    $system->run();
} catch (Exception $e) {
    // Fallback error logging if class fails completely
    file_put_contents('backup_system.log', date("Y-m-d H:i:s") . " CRITICAL: " . $e->getMessage() . "\n", FILE_APPEND);
}
