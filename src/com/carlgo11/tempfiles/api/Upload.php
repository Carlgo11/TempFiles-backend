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

			if (isset($_FILES['file']) && $_FILES['file'] !== NULL) {
				$fileArray = $_FILES['file'];
				$file = new File($fileArray);

				if (Misc::getVar('maxviews') !== NULL) $file->setMaxViews(Misc::getVar('maxviews'));
				if (Misc::getVar('password') !== NULL) $password = Misc::getVar('password');
				else $password = Misc::generatePassword(6, 20);

				$file->setDeletionPassword(Misc::generatePassword(12, 32));

				$file->setMetaData([
					'size' => $fileArray['size'],
					'name' => rawurlencode($fileArray['name']),
					'type' => $fileArray['type']
				]);

				$file->setContent(file_get_contents($fileArray['tmp_name']));

				include_once __DIR__ . '/../datastorage/DataStorage.php';
				DataStorage::saveFile($file, $password);

				// Full URI to download the file
				$completeURL = sprintf($conf['download-url'], $file->getID(), $password);

				$output = [
					'url' => $completeURL,
					'id' => $file->getID(),
					'deletepassword' => $file->getDeletionPassword()];

				if (Misc::getVar('password') === NULL) $output['password'] = $password;
				if ($file->getMaxViews() !== NULL) $output['maxviews'] = (int)$file->getMaxViews();

				syslog(LOG_INFO, $output['id'] . " created.");
				return parent::outputJSON($output, 201);
			}
		} catch (Exception $e) {
			parent::outputJSON(['error' => $e->getMessage()], 500);
		}
		return NULL;
	}
}
