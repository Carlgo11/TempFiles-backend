<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\datastorage\DataStorage;
use com\carlgo11\tempfiles\exception\BadMethod;
use com\carlgo11\tempfiles\exception\MissingEntry;
use com\carlgo11\tempfiles\Misc;
use Exception;

class Delete extends API {

	/**
	 * Delete constructor.
	 *
	 * @param string|false $method
	 */
	public function __construct($method) {
		try {
			if ($method !== 'DELETE') throw new BadMethod("Bad method. Use DELETE.");
			$id = filter_var(Misc::getVar('id'), FILTER_VALIDATE_REGEXP, ["options" => ['regexp' => '/^D([0-9]|[A-z]){13}/']]);

			if (password_verify(Misc::getVar('delete'), DataStorage::getDeletionPassword($id)))
				if (DataStorage::deleteFile($id)) http_response_code(204);
				else throw new Exception("Unable to delete file");
			else throw new MissingEntry("Bad ID or Password");
		} catch (Exception $e) {
			parent::outputJSON(['error' => $e->getMessage()], $e->getCode() ?: 400);
		}
		return NULL;
	}
}
