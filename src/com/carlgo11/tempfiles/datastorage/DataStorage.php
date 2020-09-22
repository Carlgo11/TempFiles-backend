<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;
use com\carlgo11\tempfiles\Encryption;
use com\carlgo11\tempfiles\File;
use Exception;

/**
 * High level data storage handler.
 *
 * @package com\carlgo11\tempfiles\datastorage
 * @since 2.5
 */
class DataStorage {

	/**
	 * Get a stored file.
	 *
	 * @param string $id Unique ID of the stored file.
	 * @param string $password Decryption key of the stored file.
	 * @return File Decrypted file as a {@see File} object.
	 * @throws Exception
	 * @since 2.5
	 */
	public static function getFile(string $id, string $password) {
		global $conf;

		$storage = DataStorage::getStorage();

		$storedContent = $storage->getEntryContent($id);
		$storedMetaData = $storage->getEntryMetaData($id);
		$storedEncryptionData = $storage->getFileEncryptionData($id);

		$content = Encryption::decrypt(base64_decode($storedContent), $password, $storedEncryptionData['iv'][0], $storedEncryptionData['tag'][0]);
		$metadata = Encryption::decrypt($storedMetaData, $password, $storedEncryptionData['iv'][1], $storedEncryptionData['tag'][1]);
		$metadata = explode(' ', $metadata);
		$file = new File(NULL, $id);
		$file->setContent($content);

		// Keys are lost during storage.
		$file->setMetaData(['size' => base64_decode($metadata[1]), 'name' => base64_decode($metadata[0]), 'type' => base64_decode($metadata[2])]);

		return $file;
	}

	/**
	 * Get storage method.
	 *
	 * @return FileStorage|MySQLStorage|false
	 * @since 2.5
	 */
	public static function getStorage() {
		global $conf;
		include_once __DIR__ . '/DataInterface.php';
		if ($conf['storage'] == 'File') {
			include_once __DIR__ . '/FileStorage.php';
			return new FileStorage();
		} elseif ($conf['storage'] == 'MySQL') {
			include_once __DIR__ . '/MySQLStorage.php';
			return new MySQLStorage();
		}
		return FALSE;
	}

	/**
	 * Save an uploaded file.
	 *
	 * @param File $file {@see File} object to store.
	 * @param String $password Encryption key.
	 * @return boolean Returns TRUE if successful, otherwise false.
	 * @throws Exception
	 * @since 2.5
	 */
	public static function saveFile(File $file, string $password) {
		global $conf;

		$storage = DataStorage::getStorage();

		include_once __DIR__ . '/../EncryptedFile.php';
		$encryptedFile = new EncryptedFile();
		$encryptedFile->setFileContent($file->getContent(), $password);
		$encryptedFile->setFileMetaData($file->getMetaData(), $file, $password);
		$encryptedFile->setID($file->getID());

		return $storage->saveEntry($encryptedFile, $password);
	}

	public static function setViews(File $file, string $password, int $views) {
		// TODO: Implement setViews() method.
	}

	/**
	 * Delete all files older than 24 hours.
	 *
	 * @throws Exception Throws any exceptions from the storage class
	 * @since 2.5
	 */
	public static function deleteOldFiles() {
		$storage = DataStorage::getStorage();
		foreach ($storage->listEntries() as $entry)
			DataStorage::deleteFile($entry);
	}

	/**
	 * Delete a stored file
	 *
	 * @param $id
	 * @return false|mixed
	 * @throws Exception
	 */
	public static function deleteFile($id) {
		global $conf;
		$storage = DataStorage::getStorage();
		return $storage->deleteEntry($id);
	}
}
