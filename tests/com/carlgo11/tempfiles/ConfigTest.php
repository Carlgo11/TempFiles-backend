<?php

namespace com\carlgo11\tempfiles;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase {

	public function testArrayContents() {
		global $conf;
		$this->assertIsArray($conf);

		foreach (['file-path', 'Encryption-Method', 'download-url', 'storage', 'hash-cost'] as $var) {
			$this->assertArrayHasKey($var, $conf, "Config.php doesn't include the key '{$var}'.");
		}

		$this->assertIsString($conf['file-path']);
		$this->assertIsString($conf['Encryption-Method']);
		$this->assertIsString($conf['download-url']);
		$this->assertIsString($conf['storage']);
		$this->assertIsInt($conf['hash-cost']);
	}

}
