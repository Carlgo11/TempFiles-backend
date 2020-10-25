<?php

function download($file_source, $file_target) {
	$rh = fopen($file_source, 'rb');
	$wh = fopen($file_target, 'w+b');
	if (!$rh || !$wh) {
		return false;
	}

	while (!feof($rh)) {
		if (fwrite($wh, fread($rh, 4096)) === FALSE) {
			return false;
		}
		echo '';
		flush();
	}

	fclose($rh);
	fclose($wh);

	return true;
}

function return404() {
	$notFoundURL = filter_input(INPUT_ENV, 'TMP_404_URL', FILTER_VALIDATE_URL, ['options' => ['default' => 'https://tempfiles.download/download/?404=1']]);
	header($_SERVER['SERVER_PROTOCOL'] . " 404 File Not Found");
	header("Location: $notFoundURL");
	exit;
}

$url = explode('/', strtoupper($_SERVER['REQUEST_URI']));
$id = filter_var($url[1]);
$password = filter_input(INPUT_GET, "p");

# API Download URL
$downloadURL = filter_input(INPUT_ENV, 'TMP_API_DOWNLOAD_URL', FILTER_VALIDATE_URL, ['options' => ['default' => 'https://api.tempfiles.download/download/?id=%1$s&p=%2$s']]);
$d_url = sprintf($downloadURL, $id, $password);

$result = download($d_url, "/tmp/$id.tmp");
if (!$result)
	return404();

// Execute cURL command and get response data
$response = json_decode(curl_exec(file_get_contents("/tmp/$id.tmp")));

unlink("/tmp/$id.tmp");



if ($response->data) {

	// Set headers
	header("Content-Description: File Transfer");
	header("Expires: 0");
	header("Pragma: public");
	header("Content-Type: {$response->type}");
	header("Content-Disposition: inline; filename=\"{$response->filename}\"");
	header("Content-Length: {$response->length}");

	// output file contents
	echo base64_decode($response->data);

} else return404();
exit;
