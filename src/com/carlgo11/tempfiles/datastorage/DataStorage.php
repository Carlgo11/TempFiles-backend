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

		$storage = NULL;
		if ($conf['storage'] == 'File') $storage = new FileStorage($id);
		elseif ($conf['storage'] == 'MySQL') $storage = new MySQLStorage($id);

		$storedContent = $storage->getEntryContent();
		$storedMetaData = $storage->getEntryMetaData();
		$storedEncryptionData = $storage->getFileEncryptionData();

		$content = Encryption::decrypt($storedContent, $password, $storedEncryptionData[0], $storedEncryptionData[2]);
		$metadata = Encryption::decrypt($storedMetaData, $password, $storedEncryptionData[1], $storedEncryptionData[3]);

		$file = new File(NULL, $id);
		$file->setContent($content);
		$file->setMetaData(explode(' ', $metadata));

		return $file;
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

		$storage = DataStorage::getStorage($file);

		include_once __DIR__ . '/../EncryptedFile.php';
		$encryptedFile = new EncryptedFile();
		$encryptedFile->setFileContent($file->getContent(), $password);
		$encryptedFile->setFileMetaData($file->getMetaData(), $file, $password);

		return $storage->saveEntry($encryptedFile, $password);
	}

	public static function getStorage(string $id) {
		global $conf;
		include_once __DIR__ . '/DataInterface.php';
		if ($conf['storage'] == 'File') {
			include_once __DIR__ . '/FileStorage.php';
			return new FileStorage($id);
		} elseif ($conf['storage'] == 'MySQL') {
			include_once __DIR__ . '/MySQLStorage.php';
			return new MySQLStorage($id);
		}
	}

	/**
	 * @param $id
	 * @param $password
	 * @param $deletionPassword
	 * @return false|mixed
	 * @throws Exception
	 */
	public static function deleteFile($id, $password, $deletionPassword) {
		global $conf;

		$storage = DataStorage::getStorage($id);
		if ($conf['storage'] == 'File') $storage = new FileStorage($id);
		elseif ($conf['storage'] == 'MySQL') $storage = new MySQLStorage($id);

		$storedEncryptionData = $storage->getFileEncryptionData();
		$metadata = Encryption::decrypt($storage->getEntryMetaData(), $password, $storedEncryptionData['iv'], $storedEncryptionData['tag']);

		// Get deletion password from metadata array
		$delpass = $metadata[3];
		if (password_verify($deletionPassword, $delpass)) return $storage->deleteEntry($id);

		throw new Exception("Bad ID or Password.");
	}

	public static function setViews(File $file, string $password, int $views) {
		// TODO: Implement setViews() method.
	}
}
