<?php

namespace com\carlgo11\tempfiles;

use PHPUnit\Framework\TestCase;

class FileStorageTest extends TestCase
{

	public function testSaveFile() {
		$fileContent = [
			"name" => "README.md",
			"tmp_name" => __DIR__ . '/../../../../README.md',
			"size" => 0,
			"type" => "text/markdown",
			"error" => 0
		];

		$metadata = [
			'size' => $fileContent['size'],
			'name' => $fileContent['name'],
			'type' => $fileContent['type']
		];

		$file = new File($fileContent);
		$password = Misc::generatePassword(12, 32);
		$file->setDeletionPassword(Misc::generatePassword(12, 32));
		$file->setMetaData($metadata);
		$file->setContent(file_get_contents($fileContent['tmp_name']));

		$filestorage = new FileStorage();

		// File successfully stored
		$this->assertTrue($filestorage->saveFile($file, $password));

		// File successfully decrypted and fetched
		$this->assertInstanceOf(File::class, $fetched = $filestorage->getFile($file->getID(), $password));

		$fetched_metadata = $fetched->getMetaData();
		foreach ($metadata as $k => $v) {
			//File metadata equals the decrypted metadata
			$this->assertEquals($metadata[$k], $fetched_metadata[$k]);
		}
	}


}
