<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\datastorage\DataStorage;
use com\carlgo11\tempfiles\exception\BadMethod;
use com\carlgo11\tempfiles\exception\MissingEntry;
use com\carlgo11\tempfiles\Misc;
use Exception;

class Download extends API {

	/**
	 * Download constructor.
	 *
	 * @param string|false $method HTTP method.
	 */
	public function __construct($method) {
		try {
			if ($method !== 'GET') throw new BadMethod('Bad method. Use GET.');

			$url = explode('/', strtoupper($_SERVER['REQUEST_URI']));
			$id = filter_var($url[2], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^D([0-9]|[A-z]){13}/']]);
			$password = filter_var($url[3]);
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
		} catch (Exception $e) {
			parent::outputJSON(['error' => 'File not found'], 404);
			error_log($e->getMessage());
		}
		return NULL;
	}
}
