<?php

/**
 * Miscellaneous functions
 * @since 2.0
 */
class Misc {

    /**
     * Get a parameter from either $_GET or $_POST.
     * @param string $name Name of the parameter.
     * @return string Returns parameter data if the parameter exists.
     */
    public static function getVar($name) {
        if (filter_input(INPUT_GET, $name) != NULL)
                return filter_input(INPUT_GET, $name);
        if (filter_input(INPUT_POST, $name) != NULL)
                return filter_input(INPUT_POST, $name);
    }

    /**
     * Generate a password to use for encryption.
     * 
     * This function should only be used if a password is not supplied as it's less secure than if the user chooses their own password.
     * @return string Returns a string of random characters to use as a password.
     */
    public static function generatePassword($minlength = 4, $maxlength = 10) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = rand($minlength, $maxlength);
        return substr(str_shuffle($chars), 0, $length);
    }

    /**
     * Convert metric data prefixes to bytes.
     * @author John V.
     * @link http://stackoverflow.com/a/11807179
     * @param string $from String to convert.
     * @return int Output of $from in bytes.
     */
    public static function convertToBytes($from) {
        $number = substr($from, 0, -2);
        switch (strtoupper(substr($from, -2))) {
            case "KB":
                return $number * 1024;
            case "MB":
                return $number * pow(1024, 2);
            case "GB":
                return $number * pow(1024, 3);
            case "TB":
                return $number * pow(1024, 4);
            case "PB":
                return $number * pow(1024, 5);
            default:
                return $from;
        }
    }

    /**
     * Compare max views with current views.
     * @since 2.0
     * @since 2.2 Moved from /download.php to /res/Misc.php.
     * @param int $currentviews Current views.
     * @param int $maxviews Maximum allowed views.
     * @param string $id ID of the file.
     * @return boolean Returns true if current views surpass the maximum views, otherwise returns false.
     */
    public static function compareViews($currentviews, $maxviews, string $id) {
        if (isset($currentviews) && isset($maxviews)) {
            if (($currentviews + 1) >= $maxviews) {
                return DataStorage::deleteFile($id);
            } else {
                return DataStorage::setViews(intval($maxviews), ($currentviews + 1), $id);
            }
        }
        return false;
    }

}
