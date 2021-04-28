<?php

namespace com\carlgo11\tempfiles;
/**
 * Miscellaneous functions
 *
 * @package com\carlgo11\tempfiles
 * @since 2.0
 */
class Misc
{

	/**
	 * Get a parameter from either $_GET or $_POST.
	 *
	 * @param string $name Name of the parameter.
	 * @return string Returns parameter data if the parameter exists.
	 * @since 2.4 Added default NULL return.
	 */
	public static function getVar(string $name): ?string {
		if (filter_input(INPUT_GET, $name) != NULL)
			return filter_input(INPUT_GET, $name);
		if (filter_input(INPUT_POST, $name) != NULL)
			return filter_input(INPUT_POST, $name);
		return NULL;
	}

	/**
	 * Generate a password to use for encryption.
	 *
	 * This function should only be used if a password is not supplied as it's less secure than if the user chooses their own password.
	 *
	 * @param int $minlength Minimum length of the password.
	 * @param int $maxlength Maximum length of the password.
	 * @return string Returns a string of random characters to use as a password.
	 */
	public static function generatePassword($minlength = 4, $maxlength = 10): string {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$length = rand($minlength, $maxlength);
		return substr(str_shuffle($chars), 0, $length);
	}

}
