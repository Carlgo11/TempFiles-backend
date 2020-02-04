<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\FileStorage;
use Exception;

class Cleanup extends API
{

	/**
	 * Cleanup constructor.
	 *
	 * @param string $method Request {@link https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods HTTP method}.
	 * @throws Exception Returns an Exception on wrong {@link https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods HTTP method}.
	 * @since 2.4 Changed internals to work with {@see FileStorage}
	 * @since 2.3
	 */
	public function __construct(string $method) {
		if ($method !== 'PURGE') throw new Exception("Bad HTTP method. Use PURGE.");

		$fileStorage = new FileStorage();
		$status = filter_var($fileStorage->deleteOldFiles(), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		parent::addMessage('success', $status);
		parent::outputJSON(202);
	}
}
