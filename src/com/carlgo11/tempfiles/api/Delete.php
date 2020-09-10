<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\datastorage\DataStorage;
use com\carlgo11\tempfiles\Misc;
use Exception;

class Delete extends API {

	/**
	 * Delete constructor.
	 *
	 * @throws Exception
	 */
	public function __construct() {
		$id = Misc::getVar('id');
		$password = Misc::getVar('p');
		$deletionPassword = Misc::getVar('delete');
		$storedFile = DataStorage::getFile($id, $password);

		if (password_verify($deletionPassword, $storedFile->getDeletionPassword()))
			parent::addMessage('success', DataStorage::deleteFile($id));
		else throw new Exception("Bad ID or Password.");
	}
}
