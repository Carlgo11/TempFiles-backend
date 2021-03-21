<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;
use com\carlgo11\tempfiles\exception\MissingEntry;
use DateTime;
use mysqli;

class MySQLStorage implements DataInterface {

	protected mysqli $_mysql;

	public function __construct() {
		global $conf;
		$this->_mysql = new mysqli($conf['MYSQL_HOST'], $conf['MYSQL_USER'], $conf['MYSQL_PASSWORD'], $conf['MYSQL_DATABASE'], $conf['MYSQL_PORT'])  or die("!!!!!!!!!!!!!!!! error: "+mysqli_error($this->_mysql));
	}

	public function __destruct() {
		$this->_mysql->close();
	}

	/**
	 * Get encrypted content (file data) from a stored entry.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @return string Returns base64 encoded, encrypted binary file data.
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 * @since 3.0 Throw {@see MissingEntry} exception instead of NULL.
	 */
	public function getEntryContent(string $id): ?string {
		// TODO: Implement getEntryContent() method.
	}

	/**
	 * Get encrypted metadata.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @return String|null Returns an encrypted array (split ' ') containing: [0 => name, 1=> size, 2=> type, 3=> deletion password hash, 4=> view array]
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function getEntryMetaData(string $id): ?string {
		// TODO: Implement getEntryMetaData() method.
	}

	/**
	 * Save an uploaded entry.
	 *
	 * @param EncryptedFile $file {@see EncryptedFile} object to store
	 * @param string $password Encryption key
	 * @param array|null $views Views array containing current views and max views.
	 * @return bool Returns true if file was successfully saved.
	 * @since 2.5
	 */
	public function saveEntry(EncryptedFile $file, string $password, array $views = NULL): bool {
		$expiry = (new DateTime('+1 day'))->getTimestamp();
		$query = $this->_mysql->prepare("INSERT INTO `main` (`id`, `expiry`, `views`, `metadata`) VALUES (?, ?, ?, ?);");
		$views_str = implode('/', $views);
		$meta = $file->getEncryptedMetaData();
		$query->bind_param('siss', $file, $expiry, $views_str, $meta);
		error_log($this->_mysql->error);
		error_log($query->error);
		//$query->send_long_data(0, $file->getEncryptedFileContent());
		$result = $query->execute();
		error_log($this->_mysql->error);
		error_log($query->error);
		$query->close();
		return $result;
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
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
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
	 * @throws MissingEntry Throws {@see MissingEntry} exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function getEntryExpiry(string $id): string {
		// TODO: Implement getEntryExpiry() method.
	}

	/**
	 * Get all stored entry IDs
	 *
	 * @return array|false Returns an array of all stored entries.
	 * @since 2.5
	 * @deprecated Not used by DataStorage. Will be removed in the future.
	 */
	public function listEntries() {
		// TODO: Implement listEntries() method.
	}
}
