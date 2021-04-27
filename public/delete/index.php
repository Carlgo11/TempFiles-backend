<?php

namespace com\carlgo11\tempfiles\api;
require_once __DIR__ . '/../../src/com/carlgo11/tempfiles/autoload.php';
require_once __DIR__ . '/../../src/com/carlgo11/tempfiles/api/Delete.php';

if (filter_var($_SERVER['REQUEST_METHOD']) === 'OPTIONS') {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: DELETE, OPTIONS');
	http_response_code(202); // Ignore OPTIONS requests.
}else
new Delete(filter_var($_SERVER['REQUEST_METHOD'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(DELETE)$/']]));
