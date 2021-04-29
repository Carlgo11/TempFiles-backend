<?php

namespace com\carlgo11\tempfiles\exception;

use PHPUnit\Framework\TestCase;

class BadMethodTest extends TestCase {

	public function test__construct() {
		$message = 'Bad Method. Use GET';
		$badMethod = new BadMethod($message);
		$this->assertEquals(400, $badMethod->getCode());
		$this->assertEquals($message, $badMethod->getMessage());
	}
}
