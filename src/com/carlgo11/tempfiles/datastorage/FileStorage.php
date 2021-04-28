<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\exception\MissingEntry;
use DateTime;

/**
 * Local file storage class
 *
 * Should only be called by {@see DataStorage}!
 *
 * @since 2.5
 * @package com\carlgo11\tempfiles\datastorage
 */
class FileStorage implements DataInterface {

	/**
	 * Get encrypted content (file data) from a stored entry.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @return array Returns base64 encoded, encrypted binary file data.
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 * @since 3.0 Throw {@see MissingEntry} exception instead of NULL.
	 */
	public function getEntryContent(string $id): array {
		if (!$this->entryExists($id)) throw new MissingEntry();
		global $conf;

		$file = file_get_contents($conf['file-path'] . $id);
		return json_decode($file, TRUE)['content'];
	}

	/**
	 * See if an entry with the provided ID exists.
	 *
	 * @param string $id Unique ID of the entry.
	 * @return boolean Returns TRUE if entry exists, otherwise FALSE.
	 * @since 2.5
	 */
	public function entryExists(string $id): bool {
		global $conf;
		return file_exists($conf['file-path'] . $id);
	}

	/**
	 * Get encrypted metadata.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @return array|null Returns an encrypted array (split ' ') containing: [0 => name, 1=> size, 2=> type, 3=> deletion password hash, 4=> view array]
	 * @throws MissingEntry Throws <a href="psi_element://MissingEntry">MissingEntry</a> exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function getEntryMetaData(string $id): ?array {
		if (!$this->entryExists($id)) throw new MissingEntry();
		global $conf;

		$file = file_get_contents($conf['file-path'] . $id);
		return json_decode($file, TRUE)['metadata'];
	}

	/**
	 * Get views and max views of a stored entry.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @return array|null Returns an array containing: [0 => current views, 1 => max views].
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 3.0
	 */
	public function getEntryViews(string $id): ?array {
		if (!$this->entryExists($id)) throw new MissingEntry();
		global $conf;

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		if (isset($data['views'])) return explode('/', $data['views']);
		else return NULL;
	}

	/**
	 * Save an uploaded entry.
	 *
	 * @param array $file object to store
	 * @param string $deletionPassword Deletion password hash.
	 * @param array|null $views Views array containing current views and max views.
	 * @return bool Returns true if file was successfully saved.
	 * @since 2.5
	 */
	public function saveEntry(array $file, string $deletionPassword, array $views = NULL): bool {
		global $conf;
		$newFile = fopen($conf['file-path'] . $file['id'], "w");

		$expiry = (new DateTime('+1 day'))->getTimestamp();
		$data = [
			'expiry' => $expiry,
			'metadata' => $file['metadata'],
			'delpass' => $deletionPassword,
		];
		if (isset($views)) $data['views'] = implode('/', $views);

		// Add file content to the array.
		$data['content'] = $file['content'];

		$txt = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		fwrite($newFile, $txt);
		return fclose($newFile);
	}

	/**
	 * Get the expiry date of a stored entry.
	 *
	 * @param string $id Unique ID of the entry.
	 * @return string Returns the timestamp as a string.
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function getEntryExpiry(string $id): string {
		if (!$this->entryExists($id)) throw new MissingEntry();
		global $conf;

		$file = file_get_contents($conf['file-path'] . $id);
		return json_decode($file, TRUE)['expiry'];
	}

	/**
	 * Delete a stored entry (file)
	 *
	 * @param string $id Unique ID of the entry to delete.
	 * @return bool Returns true if stored entry successfully deleted.
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function deleteEntry(string $id): bool {
		if (!$this->entryExists($id)) throw new MissingEntry();
		global $conf;
		return unlink($conf['file-path'] . $id);
	}

	/**
	 * Get all stored entry IDs
	 *
	 * @return array|false Returns an array of all stored entries.
	 * @since 2.5
	 */
	public function listEntries() {
		global $conf;
		return array_diff(scandir($conf['file-path']), array('.', '..'));
	}

	/**
	 * Update views array in stored entry file.
	 *
	 * @param string $id ID of the entry.
	 * @param int $currentViews New current views.
	 * @return bool Returns true if views successfully changed, otherwise false.
	 * @since 3.0
	 */
	public function updateEntryViews(string $id, int $currentViews): bool {
		global $conf;
		$file = file_get_contents($conf['file-path'] . $id);
		$newFile = fopen($conf['file-path'] . $id, 'w');
		$data = json_decode($file, TRUE);
		$views = explode('/', $data['views']);
		$data['views'] = "$currentViews/$views[1]";
		$txt = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		fwrite($newFile, $txt);
		return fclose($newFile);
	}

	public function getDelPassword(string $id) {
		if (!$this->entryExists($id)) throw new MissingEntry();
		global $conf;

		$file = file_get_contents($conf['file-path'] . $id);
		return json_decode($file, TRUE)['delpass'];
	}
}
