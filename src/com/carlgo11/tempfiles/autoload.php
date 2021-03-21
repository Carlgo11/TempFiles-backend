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

if ($conf['storage'] === 'MySQL') {
	if (!function_exists('mysqli_connect')) die("MySQLi not enabled on the server");

	$conf['MYSQL_HOST'] = str_replace('"', "", getenv('MYSQL_HOST'));
	$conf['MYSQL_PORT'] = str_replace('"', "",getenv('MYSQL_PORT'));
	$conf['MYSQL_USER'] = str_replace('"', "",$_ENV['MYSQL_USER']);
	$conf['MYSQL_PASSWORD'] = str_replace('"', "",$_ENV['MYSQL_PASSWORD']);
	$conf['MYSQL_DATABASE'] = str_replace('"', "",$_ENV['MYSQL_DATABASE']);
	$mysql = new mysqli($conf['MYSQL_HOST'], $conf['MYSQL_USER'], $conf['MYSQL_PASSWORD'], $conf['MYSQL_DATABASE'], $conf['MYSQL_PORT']) or die(mysqli_error($mysql));

}
