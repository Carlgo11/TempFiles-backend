<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\EncryptedFile;

interface DataInterface
{
	public function __construct(string $id);

	public function getEntryContent();

	public function getEntryMetaData();

	public function saveEntry(EncryptedFile $file, string $password);

	public function entryExists(string $id);
}
