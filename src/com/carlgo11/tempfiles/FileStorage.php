<?php
/**
 * File storage class
 *
 * Handles the storage of uploaded file.
 *
 * @since 2.4
 * @package com\carlgo11\tempfiles
 */

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
	 * Set current views for a file.
	 *
	 * @param File $file {@see File File} to change.
	 * @param int $newViews New current views.
	 * @param string $password
	 * @return bool Returns true if change was successful, otherwise FALSE.
	 * @throws Exception Throws exception if the file can't be found.
	 * @since 2.4
	 * @see File::setCurrentViews()
	 */
	public function setViews(File $file, int $newViews, string $password) {
		global $conf;
		$plaintext = file_get_contents($conf['file-path'] . $file->getID());
		$newFile = fopen($conf['file-path'] . $file, "w");
		$json = json_decode($plaintext, TRUE);
		$metadata = Encryption::encryptFileDetails($file->getMetaData(), $file->getDeletionPassword(), $newViews, $file->getMaxViews(), $password);
		$fileContent = Encryption::encryptFileContent($file->getContent(), $password);
		$iv = [$fileContent['iv'], $fileContent['tag'], $metadata['iv'], $metadata['tag']];

		$json['time'] = $file->getDateTime();
		$json['iv'] = base64_encode(implode(' ', $iv));
		$json['metadata'] = $metadata['data'];
		$json['content'] = $fileContent['data'];

		$txt = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		fwrite($newFile, $txt);
		return fclose($newFile);
	}

	/**
	 * Delete files older than 24 hours.
	 *
	 * @see Cleanup
	 * @since 2.4
	 */
	public function deleteOldFiles() {
		global $conf;
		$files = array_diff(scandir($conf['file-path']), ['.', '..']);

		foreach ($files as $k => $id) {
			$file = file_get_contents($conf['file-path'] . $id);
			$json = json_decode($file, TRUE);
			if (isset($json['time']))
				if ($this->compareTimes($json['time'])) $this->deleteFile($id);
		}
		return TRUE;
	}

	/**
	 * Compare deletion time with current time.
	 *
	 * @param int $time Deletion time passed as a UNIX timestamp.
	 * @return true|false Returns TRUE if the file is older than it's supposed deletion time. Otherwise FALSE.
	 * @since 2.4
	 */
	private function compareTimes(int $time) {
		try {
			$currentTime = new DateTime();
			$input = date_create();

			date_timestamp_set($input, $time);
		} catch (Exception $ex) {
			return FALSE;
		}
		return $input->getTimestamp() < $currentTime->getTimestamp();
	}

	/**
	 * Delete a certain file
	 *
	 * @param String $id {@see File::getID() ID} of the file to delete.
	 * @return true|false Returns true if successfully deleted, otherwise false.
	 * @see FileStorage::deleteOldFiles()
	 * @since 2.4
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
		$newFile = fopen($conf['file-path'] . $file, "w");

		$maxViews = $file->getMaxViews();
		if ($maxViews === NULL) $maxViews = 0;

		$fileContent = Encryption::encryptFileContent($file->getContent(), $password);
		$fileMetadata = Encryption::encryptFileDetails($file->getMetaData(), $file->getDeletionPassword(), 0, $maxViews, $password);

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
	 * @codeCoverageIgnore
	 * @since 2.4
	 */
	public function getFile(string $id, string $password) {
		global $conf;
		$plaintext = file_get_contents($conf['file-path'] . $id);
		$json = json_decode($plaintext, TRUE);
		$file = new File(NULL, $id);
		$iv = base64_decode($json['iv']);
		$iv_array = explode(' ', $iv);
		$file->setIV($iv_array);

		$content = Encryption::decrypt($json['content'], $password, $file->getIV()[0], $file->getIV()[1], NULL);

		if ($content === FALSE) throw new Exception("Could not decrypt content");

		$metadata_string = Encryption::decrypt(base64_decode($json['metadata']), $password, $file->getIV()[2], $file->getIV()[3], OPENSSL_RAW_DATA);

		if ($metadata_string === FALSE) throw new Exception("Could not decrypt metadata.");

		$metadata_array = explode(';', $metadata_string);
		$metadata = ['name' => base64_decode($metadata_array[0]), 'size' => base64_decode($metadata_array[1]), 'type' => base64_decode($metadata_array[2])];
		$views_array = explode(' ', base64_decode($metadata_array[4]));
		$time = date_create();
		date_timestamp_set($time, (int)$json['time']);

		if ($this->compareTimes($json['time'])) {
			$this->deleteFile($id);
			return FALSE;
		}

		$file->setContent($content);
		$file->setMetaData($metadata);
		$file->setCurrentViews((int)$views_array[0]);
		$file->setMaxViews((int)$views_array[1]);
		$file->setDeletionPassword(base64_decode($metadata_array[3]));
		$file->setDateTime($time);

		return $file;
	}
}
