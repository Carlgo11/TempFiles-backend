<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\datastorage\DataStorage;
use com\carlgo11\tempfiles\Misc;
use Exception;

class Download extends API {

	/**
	 * Download constructor.
	 *
	 * @param string $method HTTP method.
	 */
	public function __construct(string $method) {
		try {
			if ($method !== 'GET') throw new Exception("Bad method. Use GET.");

			$id = Misc::getVar('id');
			$p = Misc::getVar('p');

			include_once __DIR__ . '/../datastorage/DataStorage.php';
			$file = DataStorage::getFile($id, $p);

			if (isset($file) && $file !== FALSE) {
				$metadata = $file->getMetaData();
				$content = base64_encode($file->getContent());
				parent::outputJSON([
					"type" => $metadata['type'],
					"filename" => $metadata['name'],
					"length" => $metadata['size'],
					"data" => $content
				], 200);

				if ($file->getMaxViews()) { // max views > 0
					if ($file->getMaxViews() <= $file->getCurrentViews() + 1) DataStorage::deleteFile($id);
					else $file->setCurrentViews($file->getCurrentViews() + 1);
				}
			} else throw new Exception("File not found");
		} catch (Exception $e) {
			parent::outputJSON(['error' => $e->getMessage()], 500);
		}
		return NULL;
	}
}
