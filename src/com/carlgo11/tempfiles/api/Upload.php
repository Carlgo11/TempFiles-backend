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
				$fileContent = $_FILES['file'];
				$file = new File($fileContent);

				if (Misc::getVar('maxviews') !== NULL)
					$file->setMaxViews(Misc::getVar('maxviews'));
				if (Misc::getVar('password') !== NULL)
					$password = Misc::getVar('password');
				else
					$password = Misc::generatePassword(6, 20);

				$file->setDeletionPassword(Misc::generatePassword(12, 32));

				$metadata = [
					'size' => $fileContent['size'],
					'name' => rawurlencode($fileContent['name']),
					'type' => $fileContent['type']
				];

				$file->setMetaData($metadata);
				$file->setContent(file_get_contents($fileContent['tmp_name']));


				include_once __DIR__ . '/../datastorage/DataStorage.php';
				DataStorage::saveFile($file, $password);

				// Full URI to download the file
				$completeURL = sprintf($conf['download-url'], $file->getID(), $password);

				$output['success'] = TRUE;
				$output['url'] = $completeURL;
				$output['id'] = $file->getID();
				$output['deletepassword'] = $file->getDeletionPassword();

				if (Misc::getVar('password') === NULL) {
					$output['password-mode'] = 'Server generated.';
				} else {
					$output['password-mode'] = 'User generated.';
				}

				$output['password'] = $password;

				if ($file->getMaxViews() !== NULL) {
					$output['maxviews'] = (int)$file->getMaxViews();
				}
				parent::addMessages($output);
				return parent::outputJSON(201);
			}
			return TRUE;
		} catch (Exception $e) {
			parent::addMessage('error', $e->getMessage());
			parent::outputJSON(500);
			return FALSE;
		}
	}
}
