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
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 * @since 3.0 Throw {@see MissingEntry} exception instead of NULL.
	 */
	public function getEntryContent(string $id): ?array;

	/**
	 * Get encrypted metadata.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @return String|null Returns an encrypted array (split ' ') containing: [0 => name, 1=> size, 2=> type, 3=> deletion password hash, 4=> view array]
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function getEntryMetaData(string $id): ?array;

	/**
	 * Save an uploaded entry.
	 *
	 * @param array $file {@see EncryptedFile} object to store
	 * @param string $password Encryption key
	 * @param array|null $views Views array containing current views and max views.
	 * @return bool Returns true if file was successfully saved.
	 * @since 2.5
	 */
	public function saveEntry(array $file, string $password, array $views = NULL): bool;

	/**
	 * See if an entry with the provided ID exists.
	 *
	 * @param string $id Unique ID of the entry.
	 * @return boolean Returns TRUE if entry exists, otherwise FALSE.
	 * @since 2.5
	 */
	public function entryExists(string $id): bool;

	/**
	 * Delete a stored entry (file)
	 *
	 * @param string $id Unique ID of the entry to delete.
	 * @return bool Returns true if stored entry successfully deleted.
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function deleteEntry(string $id): bool;

	/**
	 * Get the expiry date of a stored entry.
	 *
	 * @param string $id Unique ID of the entry.
	 * @return string Returns the timestamp as a string.
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function getEntryExpiry(string $id): string;

	/**
	 * Get all stored entry IDs
	 *
	 * @return array|false Returns an array of all stored entries.
	 * @since 2.5
	 * @deprecated Not used by DataStorage. Will be removed in the future.
	 */
	public function listEntries();
}
