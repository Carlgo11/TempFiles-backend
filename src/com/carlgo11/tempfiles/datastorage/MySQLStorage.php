<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;

class MySQLStorage implements DataInterface
{

	public function getEntryContent($id) {
		// TODO: Implement getEntryContent() method.
	}

	public function getEntryMetaData($id) {
		// TODO: Implement getEntryMetaData() method.
	}

	/**
	 * Save an uploaded entry (file)
	 *
	 * @param EncryptedFile $file {@see EncryptedFile} object to store
	 * @param string $password Encryption key
	 * @return mixed
	 */
	public function saveEntry(EncryptedFile $file, string $password) {
		// TODO: Implement saveEntry() method.
	}

	/**
	 * See if an entry with the provided ID exists
	 *
	 * @param string $id ID of the entry (file)
	 * @return boolean Returns TRUE if entry exists, otherwise FALSE.
	 */
	public function entryExists(string $id) {
		// TODO: Implement entryExists() method.
	}

	/**
	 * Delete a stored entry (file)
	 *
	 * @param string $id ID of the entry to delete
	 * @return mixed
	 * @since 2.5
	 */
	public function deleteEntry(string $id) {
		// TODO: Implement deleteEntry() method.
	}

	public function listEntries() {
		// TODO: Implement listEntries() method.
	}
}
