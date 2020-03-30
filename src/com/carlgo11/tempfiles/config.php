<?php

/* NOTE:
 * For storing database credentials I use system environment variables.
 * If you'd prefer to just store the credentials in this document then,
 * replace my variables definitions with the ones I've commented.
 */

return [
	# File storage directory
	'file-path' => '/tmp/tempfiles/',
	# Allowed formats <n>MB, <n>GB, <n>TB, <n>PB.
	'max-file-size' => '100MB',
	# Encryption algorithm to use for encrypting uploads.
	'Encryption-Method' => 'aes-256-gcm',
	# Download URL
	'download-url' => 'https://d.carlgo11.com/%1$s/?p=%2$s',
	# API Download URL
	'api-download-url' => 'https://api.tempfiles.download/download/?id=%1$s&p=%2$s'
];
