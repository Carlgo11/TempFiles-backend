<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\datastorage\DataStorage;
use com\carlgo11\tempfiles\File;
use com\carlgo11\tempfiles\Misc;
use Exception;

class Upload extends API {

	/**
	 * Upload constructor.
	 *
	 * @param string $method HTTP method.
	 */
	function __construct(string $method) {
		global $conf;
		try {
			if ($method !== 'POST') throw new Exception("Bad method. Use POST.");
			if (!isset($_FILES['file']) || $_FILES['file'] === NULL) throw new Exception("No file uploaded.");

			$fileArray = $_FILES['file'];
			$file = new File($fileArray);

			if (Misc::getVar('maxviews') !== NULL) {
				$file->setMaxViews(Misc::getVar('maxviews'));
				$output['maxviews'] = (int)$file->getMaxViews();
			}

			if (Misc::getVar('password') !== NULL) $password = Misc::getVar('password');
			else {
				$password = Misc::generatePassword(6, 20);
				$output['password'] = $password;
			}

			$file->setDeletionPassword(Misc::generatePassword(12, 32));
			$file->setContent(file_get_contents($fileArray['tmp_name']));
			$file->setMetaData([
				'size' => $fileArray['size'],
				'name' => rawurlencode($fileArray['name']),
				'type' => $fileArray['type']
			]);

			if (!DataStorage::saveFile($file, $password)) throw new Exception("File-storing failed.");

			$output = [
				'url' => sprintf($conf['download-url'], $file->getID(), $password),
				'id' => $file->getID(),
				'deletepassword' => $file->getDeletionPassword()];

			syslog(LOG_INFO, $output['id'] . " created.");
			return parent::outputJSON($output, 201);
		} catch (Exception $e) {
			parent::outputJSON(['error' => $e->getMessage()], 400);
		}
		return NULL;
	}
}
