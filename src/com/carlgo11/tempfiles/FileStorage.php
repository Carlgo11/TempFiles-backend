<?php

namespace com\carlgo11\tempfiles;

use com\carlgo11\tempfiles\api\Cleanup;
use DateTime;
use Exception;

/**
 * File storage class
 *
 * Handles the storage of uploaded file.
 *
 * @since 2.4
 * @package com\carlgo11\tempfiles
 */
class FileStorage
{

	/**
	 * Delete files older than 24 hours.
	 *
	 * @since 2.4
	 * @see Cleanup
	 */
	public function deleteOldFiles() {
		global $conf;
		$files = array_diff(scandir($conf['file-path']), ['.', '..']);

		foreach ($files as $k => $id) {
			$file = file_get_contents($conf['file-path'] . $id);
			$json = json_decode($file, TRUE);
			if (isset($json['time'])) {
				$currentDate = new DateTime();
				$time = date_create();
				date_timestamp_set($time, (int)$json['time']);

				if ($time->getTimestamp() < $currentDate->getTimestamp()) $this->deleteFile($id);
			}
		}
		return TRUE;
	}

	/**
	 * Delete a certain file
	 *
	 * @param $id File {@see File::getID() ID} of the file to delete.
	 * @return true|false Returns true if successfully deleted, otherwise false.
	 * @see FileStorage::deleteOldFiles()
	 */
	public function deleteFile($id) {
		global $conf;
		return unlink($conf['file-path'] . $id);
	}

	/**
	 * Save file to storage.
	 *
	 * @param File $file {@see File File} object to upload
	 * @param string $password Encryption password
	 * @return true|false Returns true if successful, otherwise false.
	 * @throws Exception
	 * @since 2.4
	 */
	public function saveFile(File $file, string $password) {
		global $conf;
		$content = [];
		$newFile = fopen($conf['file-path'] . $file->getID(), "w");

		$fileContent = Encryption::encryptFileContent($file->getContent(), $password);
		$fileMetadata = Encryption::encryptFileDetails($file->getMetaData(), $file->getDeletionPassword(), 0, $file->getMaxViews(), $password);
		$iv = [$fileContent['iv'], $fileContent['tag'], $fileMetadata['iv'], $fileMetadata['tag']];
		$date = new DateTime('+1 day');
		$time = $date->getTimestamp();

		$content['time'] = $time;
		$content['metadata'] = $fileMetadata['data'];
		$content['iv'] = base64_encode(implode(' ', $iv));
		$content['content'] = $fileContent['data'];

		$txt = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		fwrite($newFile, $txt);
		return fclose($newFile);
	}

	/**
	 * Get File from storage
	 *
	 * @param string $id {@see File::getID() ID} of the file.
	 * @param string $password Password of the file.
	 * @return False|File Returns the saved file as a {@see File File} object.
	 * @throws Exception
	 */
	public function getFile(string $id, string $password) {
		global $conf;
		$plaintext = file_get_contents($conf['file-path'] . $id);
		$json = json_decode($plaintext, TRUE);
		$file = new File(NULL);
		$iv = base64_decode($json['iv']);
		$iv_array = explode(' ', $iv);
		$file->setIV($iv_array);

		$content = Encryption::decrypt($json['content'], $password, $file->getIV()[0], $file->getIV()[1]);

		if ($content === FALSE) throw new Exception("Could not decrypt content");

		$metadata_string = Encryption::decrypt(base64_decode($json['metadata']), $password, $file->getIV()[2], $file->getIV()[3], OPENSSL_RAW_DATA);

		if ($metadata_string === FALSE) throw new Exception("Could not decrypt metadata.");

		$metadata_array = explode(' ', $metadata_string);
		$metadata = ['name' => $metadata_array[0], 'size' => $metadata_array[1], 'type' => $metadata_array[2]];
		$views_array = explode(' ', base64_decode($metadata_array[4]));

		$file->setContent($content);
		$file->setMetaData($metadata);
		$file->setCurrentViews((int)$views_array[0]);
		$file->setMaxViews((int)$views_array[1]);
		$file->setDeletionPassword(base64_decode($metadata_array[3]));

		return $file;
	}
}
