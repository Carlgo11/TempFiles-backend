<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;
use Exception;

class FileStorage implements DataInterface
{
	protected $_id;

	public function __construct(string $id) {
		return $this->_id = $id;
	}

	public function getEntryContent() {
		global $conf;
		if (!$this->entryExists($this->_id)) return null;

		$file = file_get_contents($conf['file-path'] . $this->_id);
		$data = json_decode($file, TRUE);
		return $data['content'];
	}

	public function getEntryMetaData() {
		global $conf;
		if (!$this->entryExists($this->_id)) return null;

		$file = file_get_contents($conf['file-path'] . $this->_id);
		$data = json_decode($file, TRUE);
		return $data['metadata'];
	}

	public function getFileEncryptionData(string $id) {
		global $conf;
		if (!$this->entryExists($id)) return null;

		$file = file_get_contents($conf['file-path'] . $id);
		$data = json_decode($conf['file-path'] . $id);
		return ['iv' => $data['iv'], 'tag' => $data['tag']];
	}

	public function entryExists(string $id) {
		global $conf;
		return file_exists($conf['file-path'] . $id);
	}

	public function saveEntry(EncryptedFile $file, string $password) {
		global $conf;

		$newFile = fopen($conf['file-path'] . $this->_id, "w");
		$content = [
			'expiry' => new DateTime('+1 day').getTimestamp(),
			'metadata' => $file->getEncryptedMetaData(),
			'iv' => $file->getIV(),
			'tag' => $file->getTag(),
			'content' => base64_encode($file->getEncryptedFileContent())
		];

		$txt = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		fwrite($newFile, $txt);
		return fclose($newFile);
	}
}
