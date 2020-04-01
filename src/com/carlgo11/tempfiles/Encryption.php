<?php

namespace com\carlgo11\tempfiles;

use Exception;

/**
 * Encryption handling.
 *
 * @since 2.0
 */
class Encryption
{

	/**
	 * Encrypts and encodes the content (data) of a file.
	 *
	 * @param string $content Data to encrypt.
	 * @param string $password Password used to encrypt data.
	 * @return array Returns encoded and encrypted file content.
	 * @global array $conf Configuration variables.
	 * @throws Exception
	 * @since 2.3 Added support for AEAD cipher modes.
	 * @since 2.0
	 */
	public static function encryptFileContent(string $content, string $password) {
		global $conf;
		$cipher = $conf['Encryption-Method'];
		$iv = self::getIV($cipher);

		$data = base64_encode(openssl_encrypt($content, $cipher, $password, OPENSSL_RAW_DATA, $iv, $tag));
		/*		echo "Encryption: ";
				var_dump($cipher);
				var_dump(bin2hex($tag));
				var_dump(bin2hex($iv));
				echo "\n";*/
		return ['data' => $data, 'iv' => bin2hex($iv), 'tag' => bin2hex($tag)];
	}

	/**
	 * Create an IV (Initialization Vector) string.
	 * IV contains of random data from a "random" source. In this case the source is OPENSSL.
	 *
	 * @param string $cipher Encryption method to use.
	 * @return string Returns an IV string encoded with base64.
	 * @throws Exception
	 * @since 2.3 Added support for variable IV length.
	 * @since 2.0
	 */
	public static function getIV(string $cipher) {
		$ivLength = openssl_cipher_iv_length($cipher);
		return random_bytes($ivLength);
	}

	/**
	 * Encrypts and encodes the metadata (details) of a file.
	 *
	 * @param array $file the $_FILES[] array to use.
	 * @param string $deletionPassword Deletion password to encrypt along with the metadata.
	 * @param int $currentViews Current views of the file.
	 * @param int $maxViews Max allowable views of the file before deletion.
	 * @param string $password Password used to encrypt the data.
	 * @return array|false
	 * @throws Exception
	 * @since 2.0
	 * @since 2.2 Added $deletionPassword to the array of things to encrypt.
	 * @since 2.3 Added support for AEAD cipher modes.
	 * @global array $conf Configuration variables.
	 */
	public static function encryptFileDetails(array $file, string $deletionPassword, int $currentViews, int $maxViews, string $password) {
		global $conf;
		$cipher = $conf['Encryption-Method'];
		$iv = self::getIV($cipher);

		$deletionPass = base64_encode(password_hash($deletionPassword, PASSWORD_BCRYPT));
		$views_string = base64_encode(implode(' ', [$currentViews, $maxViews]));
		$data_array = [
			base64_encode($file['name']),
			base64_encode($file['size']),
			base64_encode($file['type']),
			$deletionPass,
			$views_string
		];
		$data_string = implode(";", $data_array);

		$data_enc = base64_encode(openssl_encrypt($data_string, $cipher, $password, OPENSSL_RAW_DATA, $iv, $tag));
		/*		echo "Encryption: ";
				var_dump($cipher);
				var_dump(bin2hex($tag));
				var_dump(bin2hex($iv));
				echo "\n";*/
		if (Encryption::decrypt(base64_decode($data_enc), $password, bin2hex($iv), bin2hex($tag), OPENSSL_RAW_DATA) != FALSE)
			return ['data' => $data_enc, 'iv' => bin2hex($iv), 'tag' => bin2hex($tag)];
		else {
			error_log("Decryption returned false");
			return FALSE;
		}
	}

	/**
	 * Decrypts data.
	 *
	 * @param string $data Data to decrypt.
	 * @param string $password Password used to decrypt.
	 * @param string $iv IV for decryption.
	 * @param string $tag AEAD tag from the data encryption.
	 * @param string $options OPENSSL options.
	 * @return string Returns decrypted data.
	 * @global array $conf Configuration variables.
	 * @since 2.0
	 * @since 2.3 Added support for AEAD cipher modes.
	 * @since 2.4 Added ability to specify OPENSSL options.
	 * @global array $conf Configuration variables.
	 */
	public static function decrypt(string $data, string $password, string $iv, string $tag = NULL, $options = NULL) {
		global $conf;
		$data = openssl_decrypt($data, $conf['Encryption-Method'], $password, $options, hex2bin($iv), hex2bin($tag));
		if (is_bool($data)) {
			/*			echo "Decryption:";
						var_dump(openssl_error_string());
						var_dump(bin2hex($tag));
						var_dump(bin2hex($iv));
						echo "\n";*/
			return FALSE;
		} else {
			return $data;
		}
	}
}
