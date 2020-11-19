<?php

namespace com\carlgo11\tempfiles;

use Exception;
use PHPUnit\Framework\TestCase;

class EncryptionTest extends TestCase
{

	public function testGetIV() {
		global $conf;
		try {
			$this->assertIsString(Encryption::getIV($conf['Encryption-Method']));
		} catch (Exception $e) {
			error_log($e);
			return FALSE;
		}
		return TRUE;
	}

	public function testEncryptFileContent() {
		$content = random_bytes(2048);
		$password = 'evO07HL470qdv5d7AyzQ6NgTk94dNUj4v4K';

		$enc_content = Encryption::encryptFileContent($content, $password);

		$this->assertIsArray($enc_content);
		$keys = ['data', 'iv', 'tag'];

		foreach ($keys as $k) {
			$this->assertArrayHasKey($k, $enc_content);
			$this->assertIsString($enc_content[$k]);
		}

//        $this->assertIsString(Encryption::decrypt($enc_content['data'], $password, $enc_content['iv'], $enc_content['tag'], NULL));
	}

	public function testEncryptFileDetails() {
		// Setup initial variables
		$metadata = ['name' => 'testfile.txt', 'size' => '4096', 'type' => 'text/txt'];
		$delpass = 'gzxHJF4MZd3Ul0KsLo8vb7SPDO';
		$currentViews = 0;
		$maxViews = 9;
		$password = '1VMy5E!71-/R8acDuO8';

		$encrypted = Encryption::encryptFileDetails($metadata, password_hash($delpass, PASSWORD_BCRYPT), $currentViews, $maxViews, $password);

		// Test $encrypted output
		$this->assertIsArray($encrypted);

		// Test content of $encrypted
		$this->assertIsString($encrypted['data']);
		$this->assertIsString($encrypted['iv']);
		$this->assertIsString($encrypted['tag']);

		$decrypted = explode(" ", Encryption::decrypt(base64_decode($encrypted['data']), $password, $encrypted['iv'], $encrypted['tag'], OPENSSL_RAW_DATA));

		// Test $decrypted output
		$this->assertIsArray($decrypted);

		// Test content of $decrypted
		$this->assertEquals($metadata['name'], base64_decode($decrypted[0]));
		$this->assertEquals($metadata['size'], base64_decode($decrypted[1]));
		$this->assertEquals($metadata['type'], base64_decode($decrypted[2]));
		$this->assertTrue(password_verify($delpass, base64_decode($decrypted[3])));

		$views = explode(" ", base64_decode($decrypted[4]));
		// Test view array
		$this->assertEquals($currentViews, $views[0]);
		$this->assertEquals($maxViews, $views[1]);
	}
}
