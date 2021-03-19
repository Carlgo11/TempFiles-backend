<?php

namespace com\carlgo11\tempfiles;

use Exception;

class EncryptedFile {

	protected string $_blob;
	protected array $_iv;
	protected array $_tag;
	protected string $_metadata;
	protected string $_id;

	public function __toString(): string {
		return $this->_id;
	}

	public function setID(string $id) {
		$this->_id = $id;
	}

	/**
	 * @param String $blob Binary blob
	 * @param String $password
	 * @throws Exception
	 */
	public function setFileContent(string $blob, string $password) {
		$data = Encryption::encryptFileContent($blob, $password);
		$this->_blob = $data['data'];
		$this->_iv[0] = $data['iv'];
		$this->_tag[0] = $data['tag'];
	}

	/**
	 * Store file metadata
	 *
	 * @param array $metadata
	 * @param File $file
	 * @param String $password
	 * @throws Exception
	 */
	public function setFileMetaData(array $metadata, File $file, string $password) {
		$data = Encryption::encryptFileDetails($metadata, $file->getDeletionPassword(), $password);
		$this->_metadata = $data['data'];
		$this->_iv[1] = $data['iv'];
		$this->_tag[1] = $data['tag'];
	}

	public function getEncryptedMetaData(): string {
		return $this->_metadata;
	}

	public function getEncryptedFileContent(): string {
		return $this->_blob;
	}

	public function getIV(): array {
		return $this->_iv;
	}

	public function getTag(): array {
		return $this->_tag;
	}
}
