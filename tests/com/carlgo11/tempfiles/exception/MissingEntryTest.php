<?php

namespace com\carlgo11\tempfiles\exception;

use PHPUnit\Framework\TestCase;

class MissingEntryTest extends TestCase {

	public function test__construct() {
		$missingEntry = new MissingEntry();
		$this->assertEquals(400, $missingEntry->getCode());
		$this->assertEquals('No file found with matching ID and Password.', $missingEntry->getMessage());
	}
}
