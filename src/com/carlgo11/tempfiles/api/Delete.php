<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\datastorage\DataStorage;
use com\carlgo11\tempfiles\exception\BadMethod;
use com\carlgo11\tempfiles\exception\MissingEntry;
use Exception;

class Delete extends API {

	/**
	 * Delete constructor.
	 *
	 * @param string|false $method
	 */
	public function __construct($method) {
		try {
			if ($method !== 'DELETE') throw new BadMethod('Bad method. Use DELETE.');
			$url = explode('/', strtoupper($_SERVER['REQUEST_URI']));
			$id = filter_var($url[2], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^D([0-9]|[A-z]){14}/']]);
			$password = filter_var($url[3]);

			if (password_verify($password, DataStorage::getDeletionPassword($id)))
				if (DataStorage::deleteFile($id)) http_response_code(204);
				else throw new Exception('Unable to delete file');
			else throw new MissingEntry('Bad ID or Password');
		} catch (Exception $e) {
			parent::outputJSON(['error' => $e->getMessage()], $e->getCode() ?: 400);
		}
		return NULL;
	}
}
