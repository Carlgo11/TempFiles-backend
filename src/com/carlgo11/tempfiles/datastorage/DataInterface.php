<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;
use com\carlgo11\tempfiles\exception\MissingEntry;

interface DataInterface {

	/**
	 * Get encrypted content (file data) from a stored entry.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @return string Returns base64 encoded, encrypted binary file data.
	 * @throws MissingEntry Throws Missing Entry exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function getEntryContent(string $id): ?string;

	/**
	 * @param string $id
	 * @return string|null
	 */
	public function getEntryMetaData(string $id): ?string;

	/**
	 * Save an uploaded entry.
	 *
	 * @param EncryptedFile $file {@see EncryptedFile} object to store
	 * @param string $password Encryption key
	 * @param array|null $views Views array containing current views and max views.
	 * @return bool Returns true if file was successfully saved.
	 * @throws MissingEntry Throws Missing Entry exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function saveEntry(EncryptedFile $file, string $password,  array $views = NULL): bool;

	/**
	 * See if an entry with the provided ID exists
	 *
	 * @param string $id ID of the entry (file)
	 * @return boolean Returns TRUE if entry exists, otherwise FALSE.
	 * @since 2.5
	 */
	public function entryExists(string $id): bool;

	/**
	 * Delete a stored entry (file)
	 *
	 * @param string $id ID of the entry to delete
	 * @return mixed
	 * @since 2.5
	 */
	public function deleteEntry(string $id): bool;

	/**
	 * Get the expiry date of a stored entry.
	 *
	 * @param string $id Unique ID of the entry.
	 * @return string Returns the timestamp as a string.
	 * @throws MissingEntry Throws Missing Entry exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function getEntryExpiry(string $id): string;

	/**
	 * @return mixed
	 * @since 2.5
	 */
	public function listEntries();
}
