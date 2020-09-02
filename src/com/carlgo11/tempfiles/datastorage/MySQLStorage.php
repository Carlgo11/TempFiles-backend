<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;

class MySQLStorage implements DataInterface
{
	public function getEntryContent(string $id) {
		// TODO: Implement getEntryContent() method.
	}

	public function getEntryMetaData(string $id) {
		// TODO: Implement getEntryMetaData() method.
	}

	public function saveEntry(EncryptedFile $file, string $password) {
		// TODO: Implement saveEntry() method.
	}

	public function entryExists(string $id) {
		// TODO: Implement entryExists() method.
	}
}
