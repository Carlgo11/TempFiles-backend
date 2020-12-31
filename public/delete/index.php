<?php

namespace com\carlgo11\tempfiles\api;
require_once __DIR__ . '/../../src/com/carlgo11/tempfiles/autoload.php';
require_once __DIR__ . '/../../src/com/carlgo11/tempfiles/api/Delete.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'OPTIONS') {
	header('Content-Type: application/json; charset=utf-8');
	http_response_code(202); // Ignore OPTIONS requests.
}else
new Delete(filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(DELETE)$/']]));
