<?php

namespace com\carlgo11\tempfiles;

use Exception;
use PHPUnit\Framework\TestCase;

class EncryptionTest extends TestCase {

	protected Encryption $_encryption;

	public function __construct(?string $name = NULL, array $data = [], $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$this->_encryption = new Encryption();
	}

	/**
	 * @depends testEncrypt
	 */
	public function testDecrypt($encrypt) {
		$decrypted = $this->_encryption::decrypt($encrypt[2], $encrypt[0]);
		$this->assertEquals($encrypt[1], $decrypted);
	}

	public function testCreateIV(): bool {
		try {
			$output = $this->_encryption::createIV('aes-256-gcm');
			$this->assertIsString($output);
			$this->assertNotEmpty($output);
			return TRUE;
		} catch (Exception $e) {
			error_log($e);
			return FALSE;
		}
	}

	public function testEncrypt() {
		try {
			$data = random_bytes(4096);
			$password = "abc123";
			$encrypted = $this->_encryption::encrypt($data, $password);
			self::assertIsArray($encrypted);
			return [$password, $data, $encrypted];
		} catch (Exception $ex) {
			error_log($ex);
			return FALSE;
		}
	}
}
