<?php
namespace com\carlgo11\tempfiles\exception;

use Exception;
use Throwable;

class BadMethod extends Exception {

	public function __construct($message = "", $code = 0, Throwable $previous = NULL) {
		parent::__construct($message, $code, $previous);
	}
}
