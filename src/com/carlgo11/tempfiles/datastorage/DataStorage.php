<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\Encryption;
use com\carlgo11\tempfiles\exception\MissingEntry;
use com\carlgo11\tempfiles\File;
use DateTime;
use Exception;

/**
 * High level data storage handler.
 *
 * @package com\carlgo11\tempfiles\datastorage
 * @since 2.5
 */
class DataStorage {

	/**
	 * Get a stored entry.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @param string $password Decryption key of the stored file.
	 * @return File Decrypted file as a {@see File} object.
	 * @throws Exception Throws exception upon file-fetching failure.
	 * @since 2.5
	 */
	public static function getFile(string $id, string $password): File {
		try {
			$storage = DataStorage::getStorage();
			$storedContent = $storage->getEntryContent($id);
			$storedMetaData = $storage->getEntryMetaData($id);
			$storedViews = $storage->getEntryViews($id);
		} catch (MissingEntry $ex) {
			throw new MissingEntry();
		}

		$content = Encryption::decrypt($storedContent, $password);
		$metadata = explode(";", Encryption::decrypt($storedMetaData, $password));

		$file = new File(NULL, $id);
		$file->setContent($content);
		//$file->setDeletionPassword(base64_decode($metadata['delpass']));
		$file->setMetaData(['size' => $metadata[0], 'name' => $metadata[1], 'type' => $metadata[2]]);
		if ($storedViews !== NULL && sizeof($storedViews) === 2) {
			$file->setCurrentViews($storedViews[0] + 1);
			$file->setMaxViews($storedViews[1]);
		}
		return $file;
	}

	/**
	 * Get storage method specified in config.
	 *
	 * @return FileStorage|MySQLStorage|false Returns the storage class specified in the config or FALSE if non is set.
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
	 * Save an uploaded entry.
	 *
	 * @param File $file {@see File} object to store.
	 * @param String $password Encryption key.
	 * @return boolean Returns TRUE if successful, otherwise false.
	 * @throws Exception
	 * @since 2.5
	 */
	public static function saveFile(File $file, string $password): bool {
		$storage = DataStorage::getStorage();

		$data = [
			'id' => $file->getID(),
			'content' => Encryption::encrypt($file->getContent(), $password),
			'metadata' => Encryption::encrypt(implode(";", $file->getMetaData()), $password),
		];

		return $storage->saveEntry($data, $password, [$file->getCurrentViews(), $file->getMaxViews()]);
	}

	/**
	 * Delete all files older than 24 hours.
	 *
	 * @throws Exception Throws any exceptions from the storage classes.
	 * @since 2.5
	 */
	public static function deleteOldFiles() {
		$storage = DataStorage::getStorage();
		foreach ($storage->listEntries() as $entry) {
			if ($storage->getEntryExpiry($entry) <= (new DateTime())->getTimeStamp())
				DataStorage::deleteFile($entry);
		}
	}

	/**
	 * Delete a stored entry.
	 *
	 * @param string $id Unique ID of the entry to delete.
	 * @return bool Returns TRUE on success & FALSE on failure.
	 * @throws Exception Throws any exceptions from the storage classes.
	 * @since 2.5
	 */
	public static function deleteFile(string $id): bool {
		$storage = DataStorage::getStorage();
		return $storage->deleteEntry($id);
	}

	/**
	 * Update current views on existing entry.
	 *
	 * @param string $id Unique ID of the stored entry.
	 * @param int $currentViews New current views.
	 * @return bool Returns TRUE if views were successfully changed.
	 * @throws Exception
	 * @since 3.0
	 */
	public static function updateViews(string $id, int $currentViews): bool {
		$storage = DataStorage::getStorage();
		return $storage->updateEntryViews($id, $currentViews);
	}
}
