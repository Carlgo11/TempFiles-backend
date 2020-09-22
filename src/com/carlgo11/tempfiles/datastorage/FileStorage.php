<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;
use DateTime;
use Exception;

/**
 * Local file storage class
 *
 * Should only be called by {@see DataStorage}!
 *
 * @package com\carlgo11\tempfiles\datastorage
 */
class FileStorage implements DataInterface {

	public function getEntryContent($id) {
		global $conf;
		if (!$this->entryExists($id)) return NULL;

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		return $data['content'];
	}

	public function entryExists(string $id) {
		global $conf;
		return file_exists($conf['file-path'] . $id);
	}

	public function getEntryMetaData($id) {
		global $conf;
		if (!$this->entryExists($id)) return NULL;

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		return $data['metadata'];
	}

	public function getFileEncryptionData($id) {
		global $conf;
		if (!$this->entryExists($id)) return NULL;

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		return ['iv' => $data['iv'], 'tag' => $data['tag']];
	}

	/**
	 * Save an uploaded entry (file)
	 *
	 * @param EncryptedFile $file {@see EncryptedFile} object to store
	 * @param string $password Encryption key
	 * @return mixed
	 * @since 2.5
	 */
	public function saveEntry(EncryptedFile $file, string $password) {
		global $conf;
		$newFile = fopen($conf['file-path'] . $file, "w");

		// Get expiry date if file already exists
		//if ($this->entryExists($file)) $expiry = $this->getExpiry($file);
		//else
			$expiry = (new DateTime('+1 day'))->getTimestamp();

		$content = [
			'expiry' => $expiry,
			'metadata' => $file->getEncryptedMetaData(),
			'iv' => $file->getIV(),
			'tag' => $file->getTag(),
			'content' => base64_encode($file->getEncryptedFileContent())
		];

		$txt = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		fwrite($newFile, $txt);
		return fclose($newFile);
	}

	private function getExpiry(string $id) {
		global $conf;
		if (!$this->entryExists($id)) return NULL;

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		return $data['expiry'];
	}

	/**
	 * Delete a stored entry (file)
	 *
	 * @param string $id ID of the entry to delete
	 * @return mixed
	 * @throws Exception
	 */
	public function deleteEntry(string $id) {
		if (!$this->entryExists($id)) throw new Exception("No file found by that ID");

		global $conf;
		return unlink($conf['file-path'] . $id);
	}

	public function listEntries() {
		global $conf;
		return array_diff(scandir($conf['file-path']), array('.', '..'));
	}
}
