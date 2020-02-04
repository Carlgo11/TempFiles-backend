<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\FileStorage;
use com\carlgo11\tempfiles\Misc;
use Exception;

class Download extends API
{

	/**
	 * Download constructor.
	 *
	 * @param string $method HTTP method.
	 * @throws Exception Throws exception if HTTP method is invalid.
	 */
	public function __construct(string $method) {
		if ($method !== 'GET') throw new Exception("Bad method. Use GET.");

		$id = Misc::getVar('id');
		$p = Misc::getVar('p');

		$fileStorage = new FileStorage();
		$file = $fileStorage->getFile($id, $p);

		if (isset($file) && $file !== FALSE) {
			$metadata = $file->getMetaData();
			$content = base64_encode($file->getContent());
			parent::addMessages([
				"success" => TRUE,
				"type" => $metadata['type'],
				"filename" => $metadata['name'],
				"length" => $metadata['size'],
				"data" => $content
			]);
			parent::outputJSON(200);

			if ($file->setCurrentViews(($file->getCurrentViews() + 1)))
				/* @TODO: Change to FileStorage */
				DataStorage::setViews($file->getMaxViews(), ($file->getCurrentViews() + 1), $file, $p);

		} else
			throw new Exception("File not found");
	}
}
