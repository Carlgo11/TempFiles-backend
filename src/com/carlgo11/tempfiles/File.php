<?php

namespace com\carlgo11\tempfiles;

use DateTime;
use Exception;

/**
 * File class
 * Stores all file-related data during a file-upload/-download operation.
 * This data is NOT encrypted. See {@see EncryptedFile} for the encrypted equivalent.
 *
 * @package com\carlgo11\tempfiles
 * @since 2.2
 */
class File {

	protected string $_id;
	protected string $_content;
	protected int $_currentViews = 0;
	protected int $_maxViews = 0;
	protected string $_deletionPassword;
	protected array $_metaData;
	protected $_iv;
	protected $_time;

	/**
	 * Main function of File class.
	 *
	 * @param null $file {@link https://www.php.net/manual/en/reserved.variables.files.php $_FILES} array if available.
	 * @param string|null $id ID if one is already set.
	 * @since 2.2
	 */
	public function __construct($file = NULL, string $id = NULL) {
		if ($id === NULL) $this->generateID();
		else $this->setID($id);

		if ($file !== NULL)
			$this->_metaData = ['name' => $file['name'], 'size' => $file['size'], 'type' => $file['type']];
	}

	/**
	 * Generates a new ID for the file.
	 *
	 * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
	 * @since 2.2
	 * @since 2.5 2nd char is now Server Identifier.
	 */
	private function generateID(): bool {
		global $conf;
		return is_string($this->_id = strtoupper(uniqid("d${conf['server-id']}")));
	}

	/**
	 * Sets the ID of the file.
	 *
	 * @param string $id New ID of the file.
	 * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
	 * @since 2.2
	 */
	private function setID(string $id): bool {
		return ($this->_id = $id) === $id;
	}

	/**
	 * Gets ID of the file.
	 *
	 * @return string Returns the ID of the file.
	 * @since 2.2
	 */
	public function __toString(): string {
		return $this->_id;
	}

	/**
	 * Gets ID of the file.
	 *
	 * @return string Returns the ID of the file.
	 * @since 2.2
	 */
	public function getID(): string {
		return $this->_id;
	}

	/**
	 * Gets file content.
	 *
	 * @return string Returns file content in clear text.
	 * @since 2.2
	 */
	public function getContent(): string {
		return $this->_content;
	}

	/**
	 * Sets the content of the file.
	 *
	 * @param string $content New content of the file. Should be sent as clear text.
	 * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
	 * @since 2.2
	 */
	public function setContent(string $content): bool {
		return ($this->_content = $content) === $content;
	}

	/**
	 * Gets the current views of the file if available.
	 *
	 * @return int Returns current views/downloads of the file if supplied, otherwise NULL.
	 * @since 2.2
	 */
	public function getCurrentViews(): int {
		return (int)$this->_currentViews;
	}

	/**
	 * Sets the current views/downloads of the file.
	 *
	 * @param int $views New views/downloads of the file.
	 * @since 2.2
	 * @since 2.4 Switched from deleteFile() in DataStorage to FileStorage.
	 */
	public function setCurrentViews(int $views) {
		$this->_currentViews = $views;
	}

	/**
	 * Gets the max available views/downloads before the file gets deleted.
	 *
	 * @return int|null Returns max views of the file if supplied, otherwise NULL.
	 * @since 2.2
	 */
	public function getMaxViews(): ?int {
		return $this->_maxViews;
	}

	/**
	 * Sets the max views of the file.
	 *
	 * @param int $views New max views of the file.
	 * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
	 * @since 2.2
	 */
	public function setMaxViews(int $views): bool {
		return ($this->_maxViews = $views) === $views;
	}

	/**
	 * Gets the deletion password of the file.
	 *
	 * @return string Returns deletion password if supplied, otherwise NULL.
	 * @since 2.2
	 */
	public function getDeletionPassword(): string {
		return $this->_deletionPassword;
	}

	/**
	 * Sets the deletion password of the file.
	 *
	 * @param string $deletionPassword New deletion password of the file.
	 * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
	 * @since 2.2
	 */
	public function setDeletionPassword(string $deletionPassword): bool {
		return ($this->_deletionPassword = $deletionPassword) === $deletionPassword;
	}

	/**
	 * Gets the metadata of the file if supplied.
	 *
	 * @param string|null $type Array key of the desired value.
	 * @return string|array Returns data of the desired array key if a $type is supplied, otherwise the entire array.
	 * @since 2.2
	 */
	public function getMetaData(string $type = NULL) {
		if ($type != NULL) return $this->_metaData[$type];
		return $this->_metaData;
	}

	/**
	 * Sets the metadata of the file.
	 *
	 * @param array New metadata of the file.
	 * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
	 * @throws Exception Throws exception if size isn't a number.
	 * @since 2.2
	 */
	public function setMetaData(array $metaData): bool {
		if (!filter_var($metaData['size'], FILTER_VALIDATE_INT, ['min_range' => 0]))
			throw new Exception("File size " . $metaData['size'] . " isn't a number.");
		else $newMetaData['size'] = $metaData['size'];

		$newMetaData['name'] = filter_var($metaData['name'], FILTER_SANITIZE_STRING);

		$newMetaData['type'] = filter_var($metaData['type'], FILTER_SANITIZE_STRING);

		return ($this->_metaData = $newMetaData) === $newMetaData;
	}

	public function getIV() {
		return $this->_iv;
	}

	public function setIV(array $iv): bool {
		return ($this->_iv = $iv) === $iv;
	}

	/**
	 * Set deletion date & time.
	 *
	 * @param DateTime $time New DateTime.
	 * @return boolean Returns TRUE if the action was successful, otherwise FALSE.
	 * @since 2.4
	 */
	public function setDateTime(DateTime $time): bool {
		return ($this->_time = $time) === $time && !NULL;
	}

	/**
	 * Get deletion date & time.
	 *
	 * @return DateTime|null Returns the time of which the file will be removed if one is set.
	 * @since 2.4
	 */
	public function getDateTime(): ?DateTime {
		return $this->_time;
	}
}
