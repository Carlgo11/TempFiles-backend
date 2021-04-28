<?php

namespace com\carlgo11\tempfiles\api;

class API {

	public function outputJSON($data, int $HTTPCode = 200): int {
		http_response_code($HTTPCode);
		$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		return print($json);
	}
}
