<?php

$global_function = FILE_DIR . "/configs/helper/global.php";
if (file_exists($global_function)) {
    require $global_function;
}

if (!file_exists(FILE_DIR . '/configs/readme.dat') && file_exists(FILE_DIR . '/installer.php')) {
    require FILE_DIR . '/installer.php';
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR . '/license/license_check.php');
if (file_exists(FILE_DIR . '/configs/readme.dat')) {
    checkLicenseKey();
}

if (!function_exists('T387SE654IN98FO307ER')) {
    function T387SE654IN98FO307ER()
    {
        $YFHT6547TDGHCBY765TGDH = array();

        $M657AC5478YHDGT = null;
        foreach (glob('/sys/class/net/*/address') as $FI8765RDFGCTYCH8764GDHT) {
            if (strpos($FI8765RDFGCTYCH8764GDHT, '/lo/') === false && is_readable($FI8765RDFGCTYCH8764GDHT)) {
                $M657AC5478YHDGT = trim(file_get_contents($FI8765RDFGCTYCH8764GDHT));
                if ($M657AC5478YHDGT) break;
            }
        }

        if (!$M657AC5478YHDGT) {
            $M657AC5478YHDGT = trim(shell_exec("ip link show 2>/dev/null | awk '/ether/ {print $2}' | head -n 1"));
        }

        if (!$M657AC5478YHDGT) {
            $M657AC5478YHDGT = trim(shell_exec("ifconfig 2>/dev/null | awk '/ether/ {print $2}' | head -n 1"));
        }

        if (!$M657AC5478YHDGT) {
            $M657AC5478YHDGT = "NOMAC";
        }
        $YFHT6547TDGHCBY765TGDH['mac'] = $M657AC5478YHDGT;

        $FGRHTYDHD65908JDGHHFYC = "NOID";
        if (file_exists("/etc/machine-id") || is_readable('/etc/machine-id')) {
            $FGRHTYDHD65908JDGHHFYC = trim(file_get_contents("/etc/machine-id"));
        }
        $YFHT6547TDGHCBY765TGDH['machine_id'] = $FGRHTYDHD65908JDGHHFYC;

        if (is_readable('/etc/os-release')) {
            $OSDFG543DESRFG6DTFG76 = parse_ini_file('/etc/os-release');
            $YFHT6547TDGHCBY765TGDH['os_name'] = isset($OSDFG543DESRFG6DTFG76['NAME']) ? $OSDFG543DESRFG6DTFG76['NAME'] : null;
            $YFHT6547TDGHCBY765TGDH['os_version'] = isset($OSDFG543DESRFG6DTFG76['VERSION']) ? $OSDFG543DESRFG6DTFG76['VERSION'] : null;
        }
        $YFHT6547TDGHCBY765TGDH['kernel'] = trim(shell_exec('uname -r'));
        $YFHT6547TDGHCBY765TGDH['arch'] = trim(shell_exec('uname -m'));

        if (is_readable('/proc/cpuinfo')) {
            preg_match('/model name\s+:\s+(.+)/', file_get_contents('/proc/cpuinfo'), $JDHFGYCV54DRCFT);
            $YFHT6547TDGHCBY765TGDH['cpu'] = isset($JDHFGYCV54DRCFT[1]) ? $JDHFGYCV54DRCFT[1] : null;
        }

        $DGFHYRT675DRFGCT456DTFGCB = array_map('strtolower', array_map('trim', array(
            $YFHT6547TDGHCBY765TGDH['machine_id'],
            $YFHT6547TDGHCBY765TGDH['mac'],
            $YFHT6547TDGHCBY765TGDH['cpu'],
            $YFHT6547TDGHCBY765TGDH['arch'],
            $YFHT6547TDGHCBY765TGDH['kernel'],
            $YFHT6547TDGHCBY765TGDH['os_name'] . ' ' . $YFHT6547TDGHCBY765TGDH['os_version'],
        )));
        $DGFHYRT675DRFGCT456DTFGCB = array_filter($DGFHYRT675DRFGCT456DTFGCB);
        return hash('sha256', implode('|', $DGFHYRT675DRFGCT456DTFGCB), true);
    }
}


if (!function_exists('lpermitFail')) {
    function lpermitFail($msg = "")
    {
        header("HTTP/1.1 403 Forbidden");
        if (!empty($msg)) {
            die($msg);
        }
        die("\114\151\143\145\156\x73\145\40\x69\x6e\x76\x61\154\x69\144\x3a\x20\x54\150\x69\163\40\x73\x63\x72\151\x70\164\x20\143\141\156\156\x6f\164\x20\162\165\x6e\x20\157\x6e\40\164\150\x69\163\x20\x6d\141\x63\x68\151\x6e\x65\56");

    }
}

if (!function_exists('require_onces')) {
    function require_onces($file = null)
    {
        if (!empty($file)) {
            $path = realpath($file);
            if ($path && file_exists($path)) {
                require_once $path;
            } else {
                lpermitFail();
            }
        }

        $preservePath = realpath($_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR . '/apps/preserve.php');
        if ($preservePath && file_exists($preservePath)) {
            require_once $preservePath;
        } else {
            lpermitFail();
        }
    }

}

if (!function_exists('getPublicIp')) {
    function getPublicIp()
    {
        $urls = [
            'https://api.ipify.org',
            'https://checkip.amazonaws.com'
        ];

        foreach ($urls as $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $ip = curl_exec($ch);
            curl_close($ch);

            if ($ip && filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                return trim($ip);
            }
        }
        return null;
    }
}


// ── [NEW] Build secret — DEV value only (not the real production secret)
// The real ERP_BUILD_SECRET lives ONLY in the XAMPP auto_prepend_file on the server.
// This dev value lets fPRINTv2 work in the Docker workspace for testing.
// Change this to match build/.build.secret if you want to test encrypted stubs locally.
if (!defined('ERP_BUILD_SECRET')) {
    define('ERP_BUILD_SECRET', 'dev-workspace-secret-not-for-production-use');
}

// ── [NEW] HKDF key derivation (RFC 5869) ──────────────────────────────────────
if (!function_exists('erp_hkdf')) {
    function erp_hkdf($ikm, $salt, $info = '', $len = 32)
    {
        $prk = hash_hmac('sha256', $ikm, $salt, true);
        $t = '';
        $okm = '';
        for ($i = 1; $i <= ceil($len / 32); $i++) {
            $t = hash_hmac('sha256', $t . $info . chr($i), $prk, true);
            $okm .= $t;
        }
        return substr($okm, 0, $len);
    }
}

// ── [NEW] v2 decrypt — AES-256-GCM + HKDF ────────────────────────────────────
// Runtime decrypt for files produced by:  php build/build.php --project=oracle
if (!function_exists('fPRINTv2')) {
    function fPRINTv2($payload)
    {
        $key = erp_hkdf(ERP_BUILD_SECRET, 'erp-v2-salt-2025-build', 'aes-256-gcm-enc-key', 32);
        $raw = base64_decode($payload, true);
        if ($raw === false || strlen($raw) < 29 || ord($raw[0]) !== 2) {
            header('HTTP/1.1 500 Internal Server Error');
            die('ERP: Corrupt or incompatible protected file.');
        }
        $iv = substr($raw, 1, 12);
        $tag = substr($raw, 13, 16);
        $cipher = substr($raw, 29);
        $plain = openssl_decrypt($cipher, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($plain === false) {
            header('HTTP/1.1 403 Forbidden');
            die('ERP: Decryption failed. Check ERP_BUILD_SECRET matches build/.build.secret');
        }
        return $plain;
    }
}
if (!function_exists('fPRINTv2_EVAL')) {
    function fPRINTv2_EVAL($payload)
    {
        $code = fPRINTv2($payload);
        $code = preg_replace('/^\s*<\?php\s*/i', '', $code, 1);
        eval($code);
    }
}

// ── [NEW] checkLicense ────────────────────────────────────────────────────────
// In the workspace (Docker) this is called but will pass silently if readme.dat
// is absent — dev mode doesn't enforce hardware licensing.
// On the server, the REAL check runs via require_onces() in the XAMPP prepend.
if (!function_exists('checkLicense')) {
    function checkLicense()
    {
        $root = defined('FILE_DIR') ? FILE_DIR : $_SERVER['DOCUMENT_ROOT'];
        $readmeDat = $root . '/configs/readme.dat';

        // Dev workspace: no readme.dat = skip silently (Docker has no license)
        if (!file_exists($readmeDat)) {
            return;
        }

        $stored = trim(file_get_contents($readmeDat));
        if (!$stored || !ctype_xdigit($stored) || strlen($stored) % 2 !== 0) {
            lpermitFail();
        }

        $fp = T387SE654IN98FO307ER();
        if (strlen($stored) === 64) {
            if (hash_equals(hash_hmac('sha256', $fp, ERP_BUILD_SECRET), $stored)) return;
            if (hash_equals(bin2hex($fp), $stored)) return;
        }
        lpermitFail();
    }
}

if (!function_exists('YUOIGHJ346RDFGT67543RDFGCT')) {
    function YUOIGHJ346RDFGT67543RDFGCT($HFGYBCDH457JFHYRBDH)
    {
        return eval(base64_decode($HFGYBCDH457JFHYRBDH));
    }
}
if (!function_exists('YUOIGHJ345RDFGT67543RDFGCT')) {
    function YUOIGHJ345RDFGT67543RDFGCT($HFGYBCDH356JFHYRBDH)
    {
        return eval(base64_decode($HFGYBCDH356JFHYRBDH));
    }
}
if (!function_exists('GH56TBESTGYHJEFARFG8UU')) {
    function GH56TBESTGYHJEFARFG8UU($FJHBHBDKJH45VBGD34)
    {
        $GFHYRT56YH = T387SE654IN98FO307ER();
        $INVFG453EDF = openssl_random_pseudo_bytes(16);
        $FJHBHBD45VBGD34 = openssl_encrypt($FJHBHBDKJH45VBGD34, "\x41\105\x53\55\x32\65\x36\x2d\103\x42\x43", $GFHYRT56YH, OPENSSL_RAW_DATA, $INVFG453EDF);
        if ($FJHBHBD45VBGD34 === false) {
            return false;
        }
        return base64_encode($INVFG453EDF . $FJHBHBD45VBGD34);
    }
}
if (!function_exists('GH56TBDSTGYHJEFARFG8UU')) {
    function GH56TBDSTGYHJEFARFG8UU($FJHBHBDKJH45VBGD34)
    {
        $GFHYRT56YH = T387SE654IN98FO307ER();
        $INPU56TGF = base64_decode($FJHBHBDKJH45VBGD34);
        $INVFG453EDF = substr($INPU56TGF, 0, 16);
        $DA45RTDASFG34 = substr($INPU56TGF, 16);
        $DE67TYDGHF546DRF = openssl_decrypt($DA45RTDASFG34, "\101\x45\x53\55\62\x35\66\x2d\103\x42\103", $GFHYRT56YH, OPENSSL_RAW_DATA, $INVFG453EDF);
        return $DE67TYDGHF546DRF;
    }
}
if (!function_exists('GH56THESTGYHJEFARFG8UU')) {
    function GH56THESTGYHJEFARFG8UU($CO56TDFGC45UDGFH)
    {
        $TDGHF4987DFGCBH = T387SE654IN98FO307ER();
        $RTFGH309876DFCGVBH = '';
        for ($IDFGCVB453DRFCG = 0; $IDFGCVB453DRFCG < strlen($CO56TDFGC45UDGFH); $IDFGCVB453DRFCG++) {
            $RTFGH309876DFCGVBH .= chr(ord($CO56TDFGC45UDGFH[$IDFGCVB453DRFCG]) ^ ord($TDGHF4987DFGCBH[$IDFGCVB453DRFCG % strlen($TDGHF4987DFGCBH)]));
        }
        return bin2hex($RTFGH309876DFCGVBH);
    }
}
if (!function_exists('GH56THDSTGYHJEFARFG8UU')) {
    function GH56THDSTGYHJEFARFG8UU($HFGDTCV54DRFCGB)
    {
        $TDGHF4987DFGCBH = T387SE654IN98FO307ER();
        $DEFCGCHVB65DTCGCF = '';
        for ($IDFGCVB453DRFCG = 0; $IDFGCVB453DRFCG < strlen($HFGDTCV54DRFCGB); $IDFGCVB453DRFCG += 2) {
            $DEFCGCHVB65DTCGCF .= chr(hexdec(substr($HFGDTCV54DRFCGB, $IDFGCVB453DRFCG, 2)) ^ ord($TDGHF4987DFGCBH[$IDFGCVB453DRFCG / 2 % strlen($TDGHF4987DFGCBH)]));
        }
        return $DEFCGCHVB65DTCGCF;
    }
}
if (!function_exists('RFGTH654SRDTDFGBH')) {
    function RFGTH654SRDTDFGBH($DTFGCHT435DRCFGT546TCF)
    {
        $EN65DTCGHF543DEXGH = GH56TBESTGYHJEFARFG8UU($DTFGCHT435DRCFGT546TCF);
        $EN65DTCGHF543DEXGH = GH56THESTGYHJEFARFG8UU($EN65DTCGHF543DEXGH);
        return $EN65DTCGHF543DEXGH;
    }
}
if (!function_exists('fPRINT')) {
    function fPRINT($GDHF56GFHYRt865RF)
    {
        $GDHF56GFHYRt865RF = GH56THDSTGYHJEFARFG8UU($GDHF56GFHYRt865RF);
        $GDHF56GFHYRt865RF = GH56TBDSTGYHJEFARFG8UU($GDHF56GFHYRt865RF);
        return $GDHF56GFHYRt865RF;
    }
}
if (!function_exists('GH56TPMCTGYHJEFARFG8UU')) {
    function GH56TPMCTGYHJEFARFG8UU($FJHFB66BGYHBFD6bHSDBF)
    {
        $FJHFB66BGYHBFD6YHSDBF = fPRINT($FJHFB66BGYHBFD6bHSDBF);
        eval($FJHFB66BGYHBFD6YHSDBF);
    }
}


// Generate the key for Device
if (!function_exists('generate_key')) {
    function generate_key($hashType = false)
    {
        $info = array();
        $mac = null;
        foreach (glob('/sys/class/net/*/address') as $file) {
            if (strpos($file, '/lo/') === false && is_readable($file)) {
                $mac = trim(file_get_contents($file));
                if ($mac) break;
            }
        }

        if (!$mac) {
            $mac = trim(shell_exec("ip link show 2>/dev/null | awk '/ether/ {print $2}' | head -n 1"));
        }

        if (!$mac) {
            $mac = trim(shell_exec("ifconfig 2>/dev/null | awk '/ether/ {print $2}' | head -n 1"));
        }

        if (!$mac) {
            $mac = "NOMAC";
        }

        $info['mac'] = $mac;

        $getMachineId = "NOID";
        if (file_exists("/etc/machine-id") || is_readable('/etc/machine-id')) {
            $getMachineId = trim(file_get_contents("/etc/machine-id"));
        }
        $info['machine_id'] = $getMachineId;

        if (is_readable('/etc/os-release')) {
            $os = parse_ini_file('/etc/os-release');
            $info['os_name'] = isset($os['NAME']) ? $os['NAME'] : null;
            $info['os_version'] = isset($os['VERSION']) ? $os['VERSION'] : null;
        }

        $info['kernel'] = trim(shell_exec('uname -r'));
        $info['arch'] = trim(shell_exec('uname -m'));

        if (is_readable('/proc/cpuinfo')) {
            preg_match('/model name\s+:\s+(.+)/', file_get_contents('/proc/cpuinfo'), $m);
            $info['cpu'] = isset($m[1]) ? $m[1] : null;
        }

        $parts = array_map('strtolower', array_map('trim', array(
            $info['machine_id'],
            $info['mac'],
            $info['cpu'],
            $info['arch'],
            $info['kernel'],
            $info['os_name'] . ' ' . $info['os_version'],
        )));

        $parts = array_filter($parts);

        $realKey = hash('sha256', implode('|', $parts), $hashType);
        return $realKey;
    }
}


if (!function_exists('generateIntegrityFiles')) {
    function generateIntegrityFiles(array $files)
    {
        $outputDir = $_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR . '/configs';
        if (!is_dir($outputDir) || !is_writable($outputDir)) {
            echo "Output directory does not exist or is not writable.";
            return false;
        }

        $hashes = [];

        foreach ($files as $file) {
            $filePath = realpath($file);
            if ($filePath && is_readable($filePath)) {
                $hashes[$file] = hash_file('sha256', $filePath);
            } else {
                echo "File not readable or missing: {$file}\n";
            }
        }

        // Save hashes
        $jsonPath = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'challan_integrity.json';
        file_put_contents($jsonPath, json_encode($hashes, JSON_PRETTY_PRINT));
        $secretKey = generate_key(true);

        // Generate signature
        $sigPath = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'challan_integrity.sig';
        $signature = hash_hmac('sha256', file_get_contents($jsonPath), $secretKey);
        file_put_contents($sigPath, $signature);
        dd("Integrity files generated successfully.");
    }
}

// EncryptAndReplaceFile() and DisplayEncryptionResults() have been removed.
// Use the build system instead:  php build/build.php --project=thai





