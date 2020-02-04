<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\FileStorage;
use com\carlgo11\tempfiles\Misc;
use Exception;

class Delete extends API
{

	/**
	 * Delete constructor.
	 *
	 * @throws Exception
	 */
	public function __construct() {
		$id = Misc::getVar('id');
		$password = Misc::getVar('p');
		$deletionPassword = Misc::getVar('delete');
		$fileStorage = new FileStorage();

		if ($file = $fileStorage->getFile($id, $password))
			$stored_deletionPassword = $file->getDeletionPassword();
		else throw new Exception("Bad ID or Password.");

		if (password_verify($deletionPassword, $stored_deletionPassword)) {
			parent::addMessage('success', (boolean)$fileStorage->deleteFile($id));
			return parent::outputJSON(200);
		}
		throw new Exception("Bad ID or Password.");
	}
}
