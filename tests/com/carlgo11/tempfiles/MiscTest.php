<?php

namespace com\carlgo11\tempfiles;

use PHPUnit\Framework\TestCase;

class MiscTest extends TestCase {


	public function testGeneratePassword() {
		$password = Misc::generatePassword(4, 10);
		$this->assertIsString($password);
		$length = strlen($password);
		$this->assertLessThanOrEqual(10, $length);
		$this->assertGreaterThanOrEqual(4, $length);
	}
}
