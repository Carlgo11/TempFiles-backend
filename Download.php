<?php

use com\carlgo11\tempfiles\datastorage\DataStorage;

require __DIR__ . '/src/com/carlgo11/tempfiles/autoload.php';

function return404() {
	$notFoundURL = filter_input(INPUT_ENV, 'TMP_404_URL', FILTER_VALIDATE_URL, ['options' => ['default' => 'https://tempfiles.download/download/?404=1']]);
	header($_SERVER['SERVER_PROTOCOL'] . " 404 File Not Found");
	header("Location: $notFoundURL");
	exit;
}

$url = explode('/', strtoupper($_SERVER['REQUEST_URI']));
$id = filter_var($url[1]);
$password = filter_input(INPUT_GET, "p");

$file = DataStorage::getFile($id, $password);

$metadata = $file->getMetaData();
$content = base64_encode($file->getContent());

if ($file->getMaxViews()) { // max views > 0
	if ($file->getMaxViews() <= $file->getCurrentViews() + 1) DataStorage::deleteFile($id);
	else $file->setCurrentViews($file->getCurrentViews() + 1);
}

// Set headers
header("Content-Description: File Transfer");
header("Expires: 0");
header("Pragma: public");
header("Content-Type: {$metadata['type']}");
header("Content-Disposition: inline; filename=\"{$metadata['name']}\"");
header("Content-Length: {$metadata['size']}");

// output file contents
echo base64_decode($content);
