<?php

namespace com\carlgo11\tempfiles;

use Exception;

/**
 * Encryption handling.
 *
 * @since 2.0
 */
class Encryption {

	/**
	 * Encrypts string with encryption algorithm specified in config
	 *
	 * @param string $input Input data to encrypt.
	 * @param string $password Plain text password.
	 * @return array Array containing encrypted version of $input.
	 * @throws Exception Throws {@see Exception} in the event of encryption error.
	 * @since 3.0
	 * @example encrypt("data", "password");
	 */
	public static function encrypt(string $input, string $password): array {
		global $conf;
		$cipher = $conf['Encryption-Method'];
		$input = str_split($input, 524288);
		$output = [];
		foreach ($input as $part) {
			$iv = self::createIV($cipher);
			$encrypted = openssl_encrypt($part, $cipher, $password, OPENSSL_RAW_DATA, $iv, $tag);
			$output[] = implode("&", [base64_encode($encrypted), base64_encode($iv), base64_encode($tag)]);
		}
		return $output;
	}

	/**
	 * Create an IV (Initialization Vector) string.
	 * IV contains of random data from a "random" source. In this case the source is OPENSSL.
	 *
	 * @param string $cipher Encryption method to use.
	 * @return string Returns an IV string encoded with base64.
	 * @throws Exception Throws {@see Exception} if unable to create IV
	 * @since 2.0
	 * @since 2.3 Added support for variable IV length.
	 * @since 3.0 Rename getIV with createIV
	 */
	public static function createIV(string $cipher): string {
		$ivLength = openssl_cipher_iv_length($cipher);
		return random_bytes($ivLength);
	}

	/**
	 *
	 * @param array $input Encrypted data to decrypt.
	 * @param string $password Plain text decryption password.
	 * @return string Returns decrypted data in the form of a clear text string.
	 * @since 3.0
	 */
	public static function decrypt(array $input, string $password): string {
		global $conf;
		$cipher = $conf['Encryption-Method'];
		$output = [];
		foreach ($input as $part) {
			$part = explode('&', $part);
			array_push($output, openssl_decrypt(base64_decode($part[0]), $cipher, $password, OPENSSL_RAW_DATA, base64_decode($part[1]), base64_decode($part[2])));
		}
		return implode('', $output);
	}

}
