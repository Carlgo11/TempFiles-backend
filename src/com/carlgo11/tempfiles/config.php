<?php

return [
	# File storage directory
	'file-path' => getenv('TMP_PATH') ?: '/tmp/tempfiles/',
	# Encryption algorithm to use for encrypting uploads.
	'Encryption-Method' => getenv('TMP_ENCRYPTION_ALGO') ?: 'aes-256-gcm',
	# Download URL
	'download-url' => getenv('TMP_DOWNLOAD_URL') ?: 'https://d.tempfiles.download/%1$s/?p=%2$s',
	# Unique server identifier
	'server-id' => getenv('TMP_SERVER_ID') ?: 1,
	# Storage method. [File, MySQL]
	'storage' => getenv('TMP_STORAGE_METHOD') ?: 'File',
	# Hashing cost used for storing deletion passwords.
	'hash-cost' => getenv('TMP_HASH_COST') ?: 10
];
