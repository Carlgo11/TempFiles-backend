<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\File;
use com\carlgo11\tempfiles\Misc;
use PHPUnit\Framework\TestCase;

class DataStorageTest extends TestCase
{

	public function testSaveFile(){
		$password = base64_encode(random_bytes(72)); // Generate random password
		$file = new File();
		$file->setContent(random_bytes(2.097*10^6)); // Generate random 2MB content
		$file->setMetaData(['size' => 2048, 'name' => 'test.jpg', 'type' => 'image/jpeg']);
		$file->setDeletionPassword(Misc::generatePassword(12, 32));
		try {
			$this->assertTrue(DataStorage::saveFile($file, $password));
			sleep(1);
			print("Fetching file...");
			$decFile = DataStorage::getFile($file->getID(), $password);
			$this->assertIsObject($decFile);
		} catch(\Exception $ex){
			$this->addWarning($ex->getMessage());
		}

	}


}
