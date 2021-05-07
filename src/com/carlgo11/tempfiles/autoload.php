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
		http_response_code(500);
		throw new Exception("Can't find core file '{$file}'");
	}
}

function createDirectory($dir) {
	if (!file_exists($dir)) mkdir($dir, 0700, TRUE);
}

function checkVars(string $name) {
	global $conf;
	if ($conf[$name] === NULL) return $conf[$name];
	if (getenv($name) !== NULL)
		return $conf[$name] = str_replace('"', '', getenv($name));
	else throw new Exception("Environment variable '{$name}' not set.");
}

// Load resources.
$conf = checkFile(__DIR__ . '/config.php');
checkFile(__DIR__ . '/Encryption.php');
checkFile(__DIR__ . '/Misc.php');
checkFile(__DIR__ . '/File.php');
checkFile(__DIR__ . '/api/API.php');
checkFile(__DIR__ . '/datastorage/DataStorage.php');
checkFile(__DIR__ . '/exception/BadMethod.php');
checkFile(__DIR__ . '/exception/MissingEntry.php');

if ($conf['storage'] === 'File') createDirectory($conf['file-path']);

if ($conf['storage'] === 'MySQL') {
	if (!function_exists('mysqli_connect')) throw new Exception("MySQLi not enabled on the server");
	$mysql = mysqli_init();
	if ($conf['MYSQL_OPTIONS']) $mysql->options($conf['MYSQL_OPTIONS']);
	if ($conf['MYSQL_TLS_KEY'] && $conf['MYSQL_TLS_CERT']) $mysql->ssl_set($conf['MYSQL_TLS_KEY'], $conf['MYSQL_TLS_CERT']);
	$mysql->real_connect(checkVars('MYSQL_HOST'), checkVars('MYSQL_USER'), checkVars('MYSQL_PASSWORD'), checkVars('MYSQL_DATABASE'), checkVars('MYSQL_PORT'), NULL, 'utf8mb4');
	if (!$mysql) throw new Exception(mysqli_error($mysql));

}
