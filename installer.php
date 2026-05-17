<?php
/**
 * ============================================================
 *  ERP Server Installer
 *  Run ONCE on first deployment.  Self-deletes after success.
 * ============================================================
 *
 *  What it does:
 *    1. generate_key()             → writes configs/readme.dat
 *    2. generate_license_key_txt() → writes license/license_key.txt (decoy JWT)
 *    3. generateIntegrityFiles()   → writes configs/challan_integrity.json
 *                                         + configs/challan_integrity.sig
 *    4. Deletes itself + install token  (cannot run again)
 *
 *  How to trigger:
 *    Web : visit  /installer.php?token=YOUR_TOKEN
 *    CLI : php installer.php --token=YOUR_TOKEN
 *
 *  Install token:
 *    Create the file  configs/install.token  on the server manually.
 *    Put any strong random string inside it.
 *    Example:  php -r "echo bin2hex(random_bytes(16));"  > configs/install.token
 *    This file is ALSO deleted after successful installation.
 *
 *  After installation:
 *    - installer.php                  → deleted
 *    - configs/install.token          → deleted
 *    - configs/readme.dat             → created (real license HMAC)
 *    - license/license_key.txt        → created (decoy JWT)
 *    - configs/challan_integrity.json → created
 *    - configs/challan_integrity.sig  → created
 * ============================================================
 */

if (!defined('FILE_DIR')) define('FILE_DIR', $_SERVER['DOCUMENT_ROOT'] ? $_SERVER['DOCUMENT_ROOT'] : realpath(__DIR__));
if (!defined('PROJECT_DIR')) define('PROJECT_DIR', '');

// ── Load helper functions if not already available (CLI mode / no auto-prepend) ─
// On web: XAMPP auto_prepend_file already loaded T387SE654IN98FO307ER + ERP_BUILD_SECRET.
// On CLI: we try to load from known locations.
if (!function_exists('T387SE654IN98FO307ER') || !defined('ERP_BUILD_SECRET')) {
    $candidates = [
        __DIR__ . '/configs/helper/functions_server_prepend.php',
        __DIR__ . '/configs/helper/functions.php',
    ];
    foreach ($candidates as $_f) {
        if (file_exists($_f)) {
            require_once $_f;
            break;
        }
    }
    unset($candidates, $_f);
}

// ── Token verification ─────────────────────────────────────────────────────────
$tokenFile = __DIR__ . '/configs/install.token';

if (!file_exists($tokenFile)) {
    _installer_die(
        'Install token file not found.<br>'
        . 'Create <code>configs/install.token</code> on the server with a random string,<br>'
        . 'then visit this page again with <code>?token=YOUR_TOKEN</code>'
    );
}

$validToken = trim(file_get_contents($tokenFile));
if (empty($validToken)) {
    _installer_die('Install token file is empty. Add a random string to configs/install.token');
}

// Get submitted token from GET, POST, or CLI --token=xxx
$submitted = '';
if (php_sapi_name() === 'cli') {
    foreach ($argv as $arg) {
        if (strpos($arg, '--token=') === 0) {
            $submitted = substr($arg, 8);
            break;
        }
    }
} else {
    $submitted = trim($_REQUEST['token'] ? $_REQUEST['token'] : '');
}

if (empty($submitted)) {
    _installer_die('No install token provided. Use: <code>?token=YOUR_TOKEN</code>');
}

if (!hash_equals($validToken, $submitted)) {
    _installer_die('Invalid install token.');
}

// ── Check if already installed ────────────────────────────────────────────────
$readmeDat = __DIR__ . '/configs/readme.dat';
if (file_exists($readmeDat) && !isset($_GET['force'])) {
    _installer_die(
        'Already installed. configs/readme.dat exists.<br>'
        . 'If you need to re-install (e.g. server hardware changed), add <code>&force=1</code>'
    );
}

// ══════════════════════════════════════════════════════════════════════════════
//  STEP 1 — generate_key()
//  Hardware fingerprint → HMAC → readme.dat
// ══════════════════════════════════════════════════════════════════════════════
function generate_key()
{
    if (!function_exists('T387SE654IN98FO307ER')) {
        _installer_die('T387SE654IN98FO307ER() not defined. Run this on the production server with the XAMPP prepend file installed.');
    }
    if (!defined('ERP_BUILD_SECRET') || strlen(ERP_BUILD_SECRET) < 32) {
        _installer_die('ERP_BUILD_SECRET is not set or too short. Set it in the XAMPP auto_prepend_file.');
    }

    $fingerprint = T387SE654IN98FO307ER();
    $licenseKey = hash_hmac('sha256', $fingerprint, ERP_BUILD_SECRET);

    $dest = __DIR__ . '/configs/readme.dat';
    if (file_put_contents($dest, $licenseKey) === false) {
        _installer_die('Cannot write configs/readme.dat — check folder permissions.');
    }

    return $licenseKey;
}

// ══════════════════════════════════════════════════════════════════════════════
//  STEP 2 — generate_license_key_txt()
//  Generates license/license_key.txt (the decoy JWT used by license_check.php).
//  Uses the same HMAC key derivation as _deriveKeyMaterial() in license_check.php.
// ══════════════════════════════════════════════════════════════════════════════
function generate_license_key_txt()
{
    if (!function_exists('T387SE654IN98FO307ER') || !defined('ERP_BUILD_SECRET')) {
        _installer_die('Cannot generate license_key.txt: missing T387SE654IN98FO307ER or ERP_BUILD_SECRET.');
    }

    $fp = T387SE654IN98FO307ER();
    $header = base64_encode('{"typ":"JWT","alg":"HS256"}');
    $payload = base64_encode(json_encode([
        'iss' => 'erp-license-system',
        'exp' => 0,             // 0 = no expiry check
        'mid' => bin2hex($fp),  // hardware-bound
    ]));
    // _deriveKeyMaterial($header) = hash('sha256', header.'|'.ERP_BUILD_SECRET)
    $lkSecret = hash('sha256', $header . '|' . ERP_BUILD_SECRET);
    $sig = hash_hmac('sha256', $header . '.' . $payload, $lkSecret);
    $token = base64_encode($header . '.' . $payload . '.' . $sig);

    $dest = __DIR__ . '/license/license_key.txt';
    if (file_put_contents($dest, $token) === false) {
        _installer_die('Cannot write license/license_key.txt — check folder permissions.');
    }

    return $token;
}

// ══════════════════════════════════════════════════════════════════════════════
//  STEP 3 — generateIntegrityFiles($files)
//  Hash each file → JSON map → HMAC-sign → .json + .sig
// ══════════════════════════════════════════════════════════════════════════════
function generateIntegrityFiles(array $files)
{
    if (!defined('ERP_BUILD_SECRET') || strlen(ERP_BUILD_SECRET) < 32) {
        _installer_die('ERP_BUILD_SECRET is not set or too short.');
    }

    $hashes = [];
    $missing = [];

    foreach ($files as $rel) {
        $abs = __DIR__ . '/' . ltrim($rel, '/');
        if (!file_exists($abs)) {
            $missing[] = $rel;
            continue;
        }
        $hashes[$abs] = hash_file('sha256', $abs);
    }

    if (!empty($missing)) {
        _installer_log('WARNING: These files were not found and will be skipped:');
        foreach ($missing as $m) {
            _installer_log('  - ' . $m);
        }
    }

    if (empty($hashes)) {
        _installer_die('No files found to hash. Check the $protectedFiles list.');
    }

    $json = json_encode($hashes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $sig = hash_hmac('sha256', $json, ERP_BUILD_SECRET);

    $jsonDest = __DIR__ . '/configs/challan_integrity.json';
    $sigDest = __DIR__ . '/configs/challan_integrity.sig';

    if (file_put_contents($jsonDest, $json) === false) {
        _installer_die('Cannot write configs/challan_integrity.json');
    }
    if (file_put_contents($sigDest, $sig) === false) {
        _installer_die('Cannot write configs/challan_integrity.sig');
    }

    return $hashes;
}

// ══════════════════════════════════════════════════════════════════════════════
//  FILES TO PROTECT AGAINST TAMPERING
//  Add/remove paths as needed.  Paths are relative to project root.
//  These files will be hashed and verified on every request by rectitude.php.
// ══════════════════════════════════════════════════════════════════════════════
$protectedFiles = [
    // Core entry point
//    'index.php',

    // License system (the real checks)
    'configs/readme.dat',

    // Decoy license check
    'license/license_check.php',
    'license/license_key.txt',

    // Guard system
    'apps/preserve.php',
    'apps/rectitude.php',
    'apps/stash.php',
    'apps/lpermit.php',

    // Encrypted config stubs (uncomment after running: php build/build.php --project=thai)
    'configs/helper/functions.php',
    'configs/common/database.conf.php',
    'configs/journal.voucher.php',
    'configs/production.conf.php',
];

// ══════════════════════════════════════════════════════════════════════════════
//  RUN THE INSTALLATION
// ══════════════════════════════════════════════════════════════════════════════
_installer_log('Starting ERP installation...');
_installer_log('');

// Step 1: Generate real license key (readme.dat)
_installer_log('Step 1: Generating license key (readme.dat)...');
$key = generate_key();
_installer_log('  Hardware fingerprint   : ' . substr(bin2hex(T387SE654IN98FO307ER()), 0, 16) . '...');
_installer_log('  License key (HMAC)     : ' . $key);
_installer_log('  Saved to               : configs/readme.dat');
_installer_log('  ✓ Done');
_installer_log('');

// Step 2: Generate decoy license_key.txt
_installer_log('Step 2: Generating decoy license token (license_key.txt)...');
generate_license_key_txt();
_installer_log('  Saved to               : license/license_key.txt');
_installer_log('  ✓ Done');
_installer_log('');

// Step 3: Generate integrity files
_installer_log('Step 3: Generating integrity protection...');
$hashes = generateIntegrityFiles($protectedFiles);
_installer_log('  Files tracked          : ' . count($hashes));
_installer_log('  Saved to               : configs/challan_integrity.json');
_installer_log('                           configs/challan_integrity.sig');
_installer_log('  ✓ Done');
_installer_log('');

// Step 4: Self-delete installer + token
_installer_log('Step 4: Cleaning up installer...');

$deleted = [];
$failed = [];

if (file_exists($tokenFile)) {
    if (@unlink($tokenFile)) {
        $deleted[] = 'configs/install.token';
    } else {
        $failed[] = 'configs/install.token  ← DELETE THIS MANUALLY';
    }
}

if (@unlink(__FILE__)) {
    $deleted[] = 'installer.php';
} else {
    $failed[] = 'installer.php  ← DELETE THIS MANUALLY';
}

foreach ($deleted as $d) {
    _installer_log('  Deleted: ' . $d);
}
foreach ($failed as $f) {
    _installer_log('  FAILED to delete: ' . $f);
}

_installer_log('');
_installer_log('════════════════════════════════════════');
_installer_log('  Installation complete!');
_installer_log('════════════════════════════════════════');
_installer_log('');
if (!empty($failed)) {
    _installer_log('ACTION REQUIRED: Manually delete the files marked above.');
}
_installer_log('Your ERP is now licensed and protected on this machine.');
_installer_log('The application is ready to use.');

_installer_output();

// ── Output helpers ─────────────────────────────────────────────────────────────
$_installer_output_buffer = [];

function _installer_log($line)
{
    global $_installer_output_buffer;
    $_installer_output_buffer[] = $line;
}

function _installer_output()
{
    global $_installer_output_buffer;
    $isCli = php_sapi_name() === 'cli';

    if ($isCli) {
        echo implode(PHP_EOL, $_installer_output_buffer) . PHP_EOL;
    } else {
        echo '<!DOCTYPE html><html><head><meta charset="utf-8">';
        echo '<title>ERP Installer</title>';
        echo '<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:40px;}';
        echo '.box{background:#16213e;border:1px solid #0f3460;border-radius:8px;padding:24px;max-width:700px;}';
        echo 'h2{color:#e94560;margin-top:0;}';
        echo '.ok{color:#4ecca3;} .warn{color:#f7c59f;} .line{padding:2px 0;}';
        echo '</style></head><body><div class="box">';
        echo '<h2>ERP Installer</h2>';
        foreach ($_installer_output_buffer as $line) {
            $cls = (strpos($line, '✓') !== false || strpos($line, 'complete') !== false) ? 'ok'
                : (strpos($line, 'FAILED') !== false || strpos($line, 'WARNING') !== false ? 'warn' : '');
            $escaped = htmlspecialchars($line);
            echo '<div class="line ' . $cls . '">' . ($line === '' ? '&nbsp;' : $escaped) . '</div>';
        }
        echo '</div></body></html>';
    }
    exit;
}

function _installer_die($msg)
{
    _installer_log('ERROR: ' . $msg);
    _installer_output();
}
