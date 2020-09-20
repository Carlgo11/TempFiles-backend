<?php


namespace com\carlgo11\tempfiles;


use Exception;

class EncryptedFile {

	protected $_blob;
	protected $_iv;
	protected $_tag;
	protected $_metadata;
	protected string $_id;

	public function __toString() {
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
		$data = Encryption::encryptFileDetails($metadata, $file->getDeletionPassword(), (int)$file->getCurrentViews(), (int)$file->getMaxViews(), $password);
		$this->_metadata = $data['data'];
		$this->_iv[1] = $data['iv'];
		$this->_tag[1] = $data['tag'];
	}

	public function getEncryptedMetaData() {
		return $this->_metadata;
	}

	public function getEncryptedFileContent() {
		return $this->_blob;
	}

	public function getIV() {
		return $this->_iv;
	}

	public function getTag() {
		return $this->_tag;
	}
}
