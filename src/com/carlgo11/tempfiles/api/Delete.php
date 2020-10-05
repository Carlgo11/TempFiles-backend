<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\datastorage\DataStorage;
use com\carlgo11\tempfiles\Misc;
use Exception;

class Delete extends API {

	/**
	 * Delete constructor.
	 *
	 * @param string $method
	 */
	public function __construct(string $method) {
		try {
			if ($method !== 'DELETE') throw new Exception("Bad method. Use DELETE.");

			$id = filter_var(Misc::getVar('id'), FILTER_VALIDATE_REGEXP, ["options" => ['regexp' => '/^D([0-9]|[A-z]){13}/']]);
			$password = Misc::getVar('p');
			$deletionPassword = Misc::getVar('delete');
			$storedFile = DataStorage::getFile($id, $password);

			if (password_verify($deletionPassword, $storedFile->getDeletionPassword()))
				if (DataStorage::deleteFile($id)) http_response_code(200);
				else throw new Exception("Unable to delete file");
			else throw new Exception("Bad ID or Password");
		} catch (Exception $e) {
			parent::outputJSON(['error' => $e->getMessage()], 500);
		}
		return NULL;
	}
}
