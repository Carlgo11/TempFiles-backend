<?php

namespace com\carlgo11\tempfiles\exception;


use Exception;
use Throwable;

class MissingEntry extends Exception {

	public function __construct($message = "No file found with matching ID and Password.", $code = 400, Throwable $previous = NULL) {
		parent::__construct($message, $code, $previous);
	}
}
