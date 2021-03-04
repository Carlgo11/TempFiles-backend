<?php

/**
 * Checks if a file exists.
 * If it doesn't, the connection dies.
 *
 * @param string $file Full path of the file.
 * @return object|null Returns the file if found.
 * @since 2.2
 */
function checkFile(string $file) {
	if (file_exists($file))
		return require_once($file);
	else {
		error_log("Can't find {$file}");
		http_response_code(500);
		die("One or more core files can't be found on the server.");
	}
}

function createDirectory($dir) {
	if (!file_exists($dir)) mkdir($dir, 0700, TRUE);
}

// Load resources.
$conf = checkFile(__DIR__ . '/config.php');

checkFile(__DIR__ . '/Encryption.php');
checkFile(__DIR__ . '/Misc.php');
checkFile(__DIR__ . '/File.php');
checkFile(__DIR__ . '/api/API.php');
checkFile(__DIR__ . '/datastorage/DataStorage.php');
checkFile(__DIR__ . '/Exceptions/BadMethod.php');
checkFile(__DIR__ . '/Exceptions/MissingEntry.php');

createDirectory($conf['file-path']);
