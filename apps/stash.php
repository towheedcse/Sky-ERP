<?php
if (!function_exists('cacheGet')) {
	function cacheGet($key, $ttl="300") {
	    $file = sys_get_temp_dir() . '/app_' . md5($key) . '.cache';

	    if (!is_file($file) || !is_readable($file)) {
		return false;
	    }

	    $mtime = @filemtime($file);
	    if ($mtime === false || (time() - $mtime) > $ttl) {
		@unlink($file);
		return false;
	    }

	    $data = @file_get_contents($file);
	    if ($data === false) {
		return false;
	    }

	    return trim($data);
	}
}

if (!function_exists('cacheSet')) {
	function cacheSet($key, $value="") {
	    $dir = sys_get_temp_dir();
	    if (!is_writable($dir)) {
		return false;
	    }

	    $file = $dir . '/app_' . md5($key) . '.cache';
	    return (bool) @file_put_contents($file, (string)$value, LOCK_EX);
	}
}

if (!function_exists('cacheRemove')) {
	function cacheRemove($key) {
	    $file = sys_get_temp_dir() . '/app_' . md5($key) . '.cache';
	    if (is_file($file)) {
		@unlink($file); // delete the cache file
		return true;
	    }
	    return false;
	}
}

if (!function_exists('cacheClearAll')) {
	function cacheClearAll() {
	    $files = glob(sys_get_temp_dir() . '/app_*.cache');
	    foreach ($files as $file) {
		@unlink($file);
	    }
	}
}





?>
