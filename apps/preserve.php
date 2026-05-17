<?php
// Only skip if APP_GUARD is already TRUE (already ran).
// APP_GUARD=false in index.php was previously blocking this — fixed.
if (defined('APP_GUARD') && APP_GUARD === false) {
    return;
}

if (!defined('APP_GUARD')) {
    define('APP_GUARD', true);
}

$status="\114\151\143\145\156\x73\145\40\x69\x6e\x76\x61\154\x69\144\x3a\x20\x54\150\x69\163\40\x73\x63\x72\151\x70\164\x20\143\141\156\156\x6f\164\x20\162\165\x6e\x20\157\x6e\40\164\150\x69\163\x20\x6d\141\x63\x68\151\x6e\x65\56";

$projectRoot = $_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR;
if (!$projectRoot) {
    lpermitFail($status);
}

$configFiles = [
    '/apps/lpermit.php',
    '/apps/stash.php',
    '/apps/rectitude.php',
];

foreach ($configFiles as $file) {
    $path = $projectRoot . $file;
    if ($path && file_exists($path)) {
        require_once $path;
    } else {
        lpermitFail($status);
    }
}

// Example usage:
//cacheRemove('license_ok');      // removes license cache
//cacheRemove('integrity_ok');    // removes integrity cache

$licenseCached = false;
if (function_exists('cacheGet') && function_exists('cacheSet')) {
    $licenseCached = cacheGet('license_ok', 300);
}

if ($licenseCached !== '1') {
    if (function_exists('checkLicense')) {
        checkLicense();
        cacheSet('license_ok', '1');
    } else {
        lpermitFail($status);
    }
}
define('LICENSE_CHECKED', true);

if (function_exists('verifyIntegrity')) {
    verifyIntegrity();
} else {
    lpermitFail($status);
}


