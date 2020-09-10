<?php

namespace com\carlgo11\tempfiles\api;

use com\carlgo11\tempfiles\datastorage\DataStorage;
use Exception;

class Cleanup extends API {

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

		DataStorage::deleteOldFiles();
		parent::addMessage('success', TRUE);
		parent::outputJSON(202);
	}
}
