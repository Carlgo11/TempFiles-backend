<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\datastorage\DataStorage;
use com\carlgo11\tempfiles\exception\BadMethod;
use com\carlgo11\tempfiles\File;
use com\carlgo11\tempfiles\Misc;
use Exception;

class Upload extends API {

	/**
	 * Upload constructor.
	 *
	 * @param string $method HTTP method.
	 */
	function __construct($method) {
		global $conf;
		try {
			//if ($method !== 'POST') throw new BadMethod("Bad method. Use POST.");
			if (!isset($_FILES['file']) || $_FILES['file'] === NULL) throw new Exception("No file uploaded.");

			$fileArray = $_FILES['file'];
			$file = new File($fileArray);
			$output = [];

			if (!is_null(Misc::getVar('maxviews'))) {
				$file->setMaxViews(Misc::getVar('maxviews') + 1);
				$output['maxviews'] = (int)$file->getMaxViews();
			}

			if (Misc::getVar('password') !== NULL) $password = Misc::getVar('password');
			else {
				$password = Misc::generatePassword(6, 20);
				$output['password'] = $password;
			}

			$deletionPassword = Misc::generatePassword(12, 32);
			$options = ['options' => filter_var($conf['hash-cost'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 10, 'max_range' => 35]])];
			$deletionPass = password_hash($deletionPassword, PASSWORD_BCRYPT, $options);
			$file->setDeletionPassword($deletionPass);
			$file->setContent(file_get_contents($fileArray['tmp_name']));
			$file->setMetaData([
				'size' => $fileArray['size'],
				'name' => rawurlencode($fileArray['name']),
				'type' => $fileArray['type']
			]);

			if (!DataStorage::saveFile($file, $password)) throw new Exception("File-storing failed.");

			$output = array_merge($output, [
				'url' => sprintf($conf['download-url'], $file->getID(), $password),
				'id' => $file->getID(),
				'deletepassword' => $deletionPassword]);

			syslog(LOG_INFO, $output['id'] . " created.");
			return parent::outputJSON($output, 201);
		} catch (Exception $e) {
			parent::outputJSON(['error' => $e->getMessage()], $e->getCode() ?: 400);
		}
		return NULL;
	}
}
