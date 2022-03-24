<?php

namespace com\carlgo11\tempfiles\api;
require_once __DIR__ . '/../src/com/carlgo11/tempfiles/autoload.php';

switch ($_SERVER['REQUEST_METHOD']) {
	case "GET":
		require_once __DIR__ . '/../src/com/carlgo11/tempfiles/api/Download.php';
		new Download(filter_var($_SERVER['REQUEST_METHOD'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(GET)$/']]));
		break;
	case "POST":
		require_once __DIR__ . '/../src/com/carlgo11/tempfiles/api/Upload.php';
		new Upload(filter_var($_SERVER['REQUEST_METHOD'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(POST)$/']]));
		break;
	case "DELETE":
		require_once __DIR__ . '/../src/com/carlgo11/tempfiles/api/Delete.php';
		new Delete(filter_var($_SERVER['REQUEST_METHOD'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(DELETE)$/']]));
		break;
	case "OPTIONS":
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
		http_response_code(202);
		break;
}
