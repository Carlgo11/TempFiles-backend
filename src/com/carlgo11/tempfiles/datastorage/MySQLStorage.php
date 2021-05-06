<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\exception\MissingEntry;
use DateTime;
use mysqli;

class MySQLStorage implements DataInterface {

	protected mysqli $_mysql;

	public function __construct() {
		global $mysql;
		$this->_mysql = $mysql;
	}

	public function __destruct() {
		$this->_mysql->close();
	}

	/**
	 * Get encrypted content (file data) from a stored entry.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @return array Returns base64 encoded, encrypted binary file data.
	 * @since 2.5
	 * @since 3.0 Throw {@see MissingEntry} exception instead of NULL.
	 */
	public function getEntryContent(string $id): ?array {
		// TODO: Implement getEntryContent() method.
	}

	/**
	 * Get encrypted metadata.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @since 2.5
	 */
	public function getEntryMetaData(string $id): ?array {
		// TODO: Implement getEntryMetaData() method.
	}

	/**
	 * Save an uploaded entry.
	 *
	 * @param array $file {@see EncryptedFile} object to store.
	 * @param string $deletionPassword Deletion password hash.
	 * @param array|null $views Views array containing current views and max views.
	 * @return bool Returns true if file was successfully saved.
	 * @since 2.5
	 * @since 3.0 Split into 3 tables
	 */
	public function saveEntry(array $file, string $deletionPassword, array $views = NULL): bool {
		global $mysql;
		$null = NULL;
		// INSERT INTO MAIN TABLE
		$expiry = (new DateTime('+1 day'))->getTimestamp();
		if ($views !== NULL)
			$views_str = implode('/', $views);
		else
			$views_str = NULL;
		$query = $mysql->prepare("INSERT INTO `main` (`id`, `expiry`, `views`, `delpass`) VALUES (?, ?, ?, ?)");
		$query->bind_param('siss', $file['id'], $expiry, $views_str, $deletionPassword);
		$result[] = $query->execute();
		$query->close();

		// INSERT CONTENT
		foreach ($file['content'] as $part => $content) {
			$query = $mysql->prepare("INSERT INTO `content`  (`id`, `part`, `data`) VALUES (?, ?, ?)");
			$query->bind_param('sib', $file['id'], $part, $null);
			$query->send_long_data(2, $content);
			$result[] = $query->execute();
			$query->close();
		}

		// INSERT METADATA
		foreach ($file['metadata'] as $part => $content) {
			$query = $mysql->prepare("INSERT INTO `metadata`  (`id`, `part`, `data`) VALUES (?, ?, ?)");
			$query->bind_param('sis', $file['id'], $part, $content);
			//$query->send_long_data(3, $content);
			$result[] = $query->execute();
			$query->close();
		}
		return !in_array(FALSE, $result);
	}

	/**
	 * See if an entry with the provided ID exists.
	 *
	 * @param string $id Unique ID of the entry.
	 * @return boolean Returns TRUE if entry exists, otherwise FALSE.
	 * @since 2.5
	 */
	public function entryExists(string $id): bool {
		// TODO: Implement entryExists() method.
	}

	/**
	 * Delete a stored entry (file)
	 *
	 * @param string $id Unique ID of the entry to delete.
	 * @return bool Returns true if stored entry successfully deleted.
	 * @since 2.5
	 */
	public function deleteEntry(string $id): bool {
		// TODO: Implement deleteEntry() method.
	}

	/**
	 * Get the expiry date of a stored entry.
	 *
	 * @param string $id Unique ID of the entry.
	 * @return string Returns the timestamp as a string.
	 * @since 2.5
	 */
	public function getEntryExpiry(string $id): string {
		// TODO: Implement getEntryExpiry() method.
	}

	/**
	 * Get all stored entry IDs
	 *
	 * @since 2.5
	 * @deprecated Not used by DataStorage. Will be removed in the future.
	 */
	public function listEntries() {
		// TODO: Implement listEntries() method.
	}

	public function getDelPassword(string $id) {
		// TODO: Implement getDelPassword() method.
	}
}
