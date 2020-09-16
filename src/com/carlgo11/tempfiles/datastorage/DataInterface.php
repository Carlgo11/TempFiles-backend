<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;

interface DataInterface {


	public function getEntryContent($id);

	public function getEntryMetaData($id);

	/**
	 * Save an uploaded entry (file)
	 *
	 * @param EncryptedFile $file {@see EncryptedFile} object to store
	 * @param string $password Encryption key
	 * @return mixed
	 * @since 2.5
	 */
	public function saveEntry(EncryptedFile $file, string $password);

	/**
	 * See if an entry with the provided ID exists
	 *
	 * @param string $id ID of the entry (file)
	 * @return boolean Returns TRUE if entry exists, otherwise FALSE.
	 * @since 2.5
	 */
	public function entryExists(string $id);

	/**
	 * Delete a stored entry (file)
	 *
	 * @param string $id ID of the entry to delete
	 * @return mixed
	 * @since 2.5
	 */
	public function deleteEntry(string $id);

	/**
	 * @return mixed
	 * @since 2.5
	 */
	public function listEntries();
}
