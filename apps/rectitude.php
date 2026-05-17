<?php

if (!function_exists('verifyIntegrity')) {
    function verifyIntegrity()
    {
        $einfo = "\114\151\143\145\156\x73\145\40\x69\x6e\x76\x61\154\x69\144\x3a\x20\x54\150\x69\163\40\x73\x63\x72\151\x70\164\x20\143\141\156\156\x6f\164\x20\162\165\x6e\x20\157\x6e\40\164\150\x69\163\x20\x6d\141\x63\x68\151\x6e\x65\56";
        $esinfo = "\103\157\144\x65\x20\164\x61\x6d\x70\145\162\x69\156\147\40\x64\145\164\145\143\164\145\x64\x2e";
        $cacheKey = 'integrity_ok';
        $cached = cacheGet($cacheKey);

        if ($cached === '1') {
            if (!defined('INTEGRITY_OK')) {
                define('INTEGRITY_OK', true);
            }
            return;
        }

        $baseDir = realpath($_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR);
        if (!$baseDir) {
            lpermitFail($einfo);
        }

        $jsonFile = $baseDir . '/configs/challan_integrity.json';
        $sigFile = $baseDir . '/configs/challan_integrity.sig';

        if (!is_readable($jsonFile) || !is_readable($sigFile)) {
            lpermitFail($esinfo);
        }

        $json = file_get_contents($jsonFile);
        $sig = trim(file_get_contents($sigFile));

        if ($json === '' || $sig === '') {
            lpermitFail($esinfo);
        }

        $calcSig = hash_hmac('sha256', $json, ERP_BUILD_SECRET);

        if (!hash_equals($calcSig, $sig)) {
            lpermitFail($esinfo);
        }

        $hashes = json_decode($json, true);
        if (!is_array($hashes)) {
            lpermitFail($esinfo);
        }

        foreach ($hashes as $file => $expectedHash) {
            if (!file_exists($file)) {
                lpermitFail($esinfo);
            }

            $actualHash = hash_file('sha256', $file);

            if (!hash_equals($actualHash, $expectedHash)) {
                lpermitFail($esinfo);
            }
        }

        cacheSet($cacheKey, '1', 3600);

        if (!defined('INTEGRITY_OK')) {
            define('INTEGRITY_OK', true);
        }
    }
}

