<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;
use com\carlgo11\tempfiles\exception\MissingEntry;
use DateTime;
use Exception;

/**
 * Local file storage class
 *
 * Should only be called by {@see DataStorage}!
 *
 * @since 2.5
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

	public function entryExists(string $id): bool {
		global $conf;
		return file_exists($conf['file-path'] . $id);
	}

	/**
	 * @param $id
	 * @return String|null Returns an encrypted array (split ' ') containing: [0 => name, 1=> size, 2=> type, 3=> deletion password hash, 4=> view array]
	 */
	public function getEntryMetaData($id): ?string {
		global $conf;
		if (!$this->entryExists($id)) throw new MissingEntry();

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		return $data['metadata'];
	}

	public function getEntryViews($id): ?array {
		global $conf;
		if (!$this->entryExists($id)) throw new MissingEntry();

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		$views = NULL;
		if (isset($data['views'])) $views = explode('/', $data['views']);
		return $views;
	}

	public function getFileEncryptionData($id): ?array {
		global $conf;
		if (!$this->entryExists($id)) throw new MissingEntry();

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		return ['iv' => $data['iv'], 'tag' => $data['tag']];
	}

	/**
	 * Get the expiry date of an entry.
	 *
	 * @param string $id ID of the entry.
	 * @return string Returns the timestamp as a string.
	 * @throws MissingEntry Throws Missing Entry exception if no entry with the ID exists.
	 * @since 2.5
	 */
	public function getEntryExpiry(string $id): string {
		global $conf;
		if (!$this->entryExists($id)) throw new MissingEntry();

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		return $data['expiry'];
	}

	/**
	 * Save an uploaded entry (file)
	 *
	 * @param EncryptedFile $file {@see EncryptedFile} object to store
	 * @param string $password Encryption key
	 * @param array|null $views
	 * @return mixed
	 * @since 2.5
	 */
	public function saveEntry(EncryptedFile $file, string $password, array $views = NULL) {
		global $conf;
		$newFile = fopen($conf['file-path'] . $file, "w");

		// Get expiry date if file already exists
		if (($expiry = $this->getExpiry($file)) === NULL) $expiry = (new DateTime('+1 day'))->getTimestamp();

		$content = [
			'expiry' => $expiry,
			'metadata' => $file->getEncryptedMetaData(),
			'iv' => $file->getIV(),
			'tag' => $file->getTag(),
			'content' => base64_encode($file->getEncryptedFileContent())
		];
		if(isset($views)) $content['views'] = implode('/', $views);

		$txt = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		fwrite($newFile, $txt);
		return fclose($newFile);
	}

	/**
	 * Get expiry time of an already stored file.
	 *
	 * @param string $id ID of the specific file.
	 * @return int|null Returns expiry time as a UNIX Timestamp string if one is set. Returns NULL on failure.
	 * @since 2.5
	 */
	private function getExpiry(string $id): ?int {
		if (!$this->entryExists($id)) throw new MissingEntry();
		global $conf;

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($file, TRUE);
		if ($data === NULL || $data === FALSE) return NULL;
		return $data['expiry'];
	}

	/**
	 * Delete a stored entry (file)
	 *
	 * @param string $id ID of the entry to delete
	 * @return bool
	 * @throws Exception
	 * @since 2.5
	 */
	public function deleteEntry(string $id): bool {
		if (!$this->entryExists($id)) throw new MissingEntry();

		global $conf;
		return unlink($conf['file-path'] . $id);
	}

	public function listEntries() {
		global $conf;
		return array_diff(scandir($conf['file-path']), array('.', '..'));
	}

	public function updateEntryViews($id, $currentViews): bool {
		global $conf;
		$file = file_get_contents($conf['file-path'] . $id);
		$newFile = fopen($conf['file-path'] . $id, "w");
		$data = json_decode($file, TRUE);
		$views = explode('/', $data['views']);
		$data['views'] = "$currentViews/$views[1]";
		$txt = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		fwrite($newFile, $txt);
		return fclose($newFile);
	}
}
