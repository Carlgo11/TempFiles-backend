<?php

return [
	# File storage directory
	'file-path' => getenv('TMP_PATH') ?: '/tmp/tempfiles/',
	# Allowed formats <n>MB, <n>GB, <n>TB, <n>PB.
	'max-file-size' => getenv('TMP_MAX_SIZE') ?: '128MB',
	# Encryption algorithm to use for encrypting uploads.
	'Encryption-Method' => getenv('TMP_ENCRYPTION_ALGO') ?: 'aes-256-gcm',
	# Download URL
	'download-url' => getenv('TMP_DOWNLOAD_URL') ?: 'https://d.carlgo11.com/%1$s/?p=%2$s',
	# API Download URL
	'api-download-url' => getenv('TMP_API_DOWNLOAD_URL') ?: 'https://api.tempfiles.download/download/?id=%1$s&p=%2$s',
	'storage' => 'File'
];
