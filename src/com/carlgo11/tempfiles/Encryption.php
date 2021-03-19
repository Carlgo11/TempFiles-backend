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
	 * Encrypts and encodes the content (data) of a file.
	 *
	 * @param string $content Data to encrypt.
	 * @param string $password Password used to encrypt data.
	 * @return array Returns encoded and encrypted file content.
	 * @throws Exception Throws {@see Exception} if encryption failed.
	 * @global array $conf Configuration variables.
	 * @since 2.0
	 * @since 2.3 Added support for AEAD cipher modes.
	 */
	public static function encryptFileContent(string $content, string $password): ?array {
		global $conf;
		$cipher = $conf['Encryption-Method'];
		$iv = self::createIV($cipher);
		$data = base64_encode(openssl_encrypt($content, $cipher, $password, OPENSSL_RAW_DATA, $iv, $tag));

		// Test if encrypted data is able to be decrypted
		if (Encryption::decrypt(base64_decode($data), $password, bin2hex($iv), bin2hex($tag), OPENSSL_RAW_DATA) != FALSE)
			return ['data' => $data, 'iv' => bin2hex($iv), 'tag' => bin2hex($tag)];
		if (is_bool($data)) throw new Exception(openssl_error_string());
		return NULL;
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
	 * Decrypts data.
	 *
	 * @param string $input Data to decrypt.
	 * @param string $password Password used to decrypt.
	 * @param string $iv IV for decryption.
	 * @param string|null $tag AEAD tag from the data encryption.
	 * @param array|null $options OPENSSL options.
	 * @return string Returns decrypted data.
	 * @throws Exception Throws {@see Exception} if decryption failed.
	 * @global array $conf Configuration variables.
	 * @since 2.0
	 * @since 2.3 Added support for AEAD cipher modes.
	 * @since 2.4 Added ability to specify OPENSSL options.
	 * @global array $conf Configuration variables.
	 */
	public static function decrypt(string $input, string $password, string $iv, string $tag = NULL, $options = NULL): string {
		global $conf;
		$data = openssl_decrypt($input, $conf['Encryption-Method'], $password, $options, hex2bin($iv), hex2bin($tag));
		if (is_bool($data)) throw new Exception(openssl_error_string());
		return $data;
	}

	/**
	 * Encrypts and encodes the metadata (details) of a file.
	 *
	 * @param array $metadata the $_FILES[] array to use.
	 * @param string $deletionPassword Deletion password to encrypt along with the metadata.
	 * @param string $password Password used to encrypt the data.
	 * @return array Returns array of [0 => encrypted string, 1 => encryption IV, 2 => encryption tag]
	 * @throws Exception Throws {@see Exception} if encryption failed.
	 * @since 2.0
	 * @since 2.2 Added $deletionPassword to the array of things to encrypt.
	 * @since 2.3 Added support for AEAD cipher modes.
	 * @global array $conf Configuration variables.
	 */
	public static function encryptFileDetails(array $metadata, string $deletionPassword, string $password): array {
		global $conf;
		$cipher = $conf['Encryption-Method'];
		$iv = self::createIV($cipher);

		$data = [
			base64_encode($metadata['name']),
			base64_encode($metadata['size']),
			base64_encode($metadata['type']),
			base64_encode($deletionPassword),
		];

		$encrypted_string = base64_encode(openssl_encrypt(implode(' ', $data), $cipher, $password, OPENSSL_RAW_DATA, $iv, $tag));

		// Test if encrypted data is able to be decrypted
		if (Encryption::decrypt(base64_decode($encrypted_string), $password, bin2hex($iv), bin2hex($tag), OPENSSL_RAW_DATA) != FALSE)
			return ['data' => $encrypted_string, 'iv' => bin2hex($iv), 'tag' => bin2hex($tag)];
		throw new Exception(openssl_error_string());
	}
}
