<?php

namespace com\carlgo11\tempfiles\datastorage;

use com\carlgo11\tempfiles\exception\MissingEntry;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

class FileStorageTest extends TestCase {

	protected FileStorage $fileStorage;
	private string $deletePassword = 'delpass';
	private array $views = [1, 3];
	private string $id = 'DTEST';
	private array $content = ['FILE CONTENT'];
	private array $metadata = [];

	public function __construct(?string $name = NULL, array $data = [], $dataName = '') {
		parent::__construct($name, $data, $dataName);
		include_once 'src/com/carlgo11/tempfiles/datastorage/DataInterface.php';
		include_once 'src/com/carlgo11/tempfiles/datastorage/FileStorage.php';
		$this->fileStorage = new FileStorage();
	}

	/**
	 * @depends testSaveEntry
	 * @throws MissingEntry
	 */
	public function testGetEntryContent() {
		$result = $this->fileStorage->getEntryContent($this->id);
		$this->assertIsArray($result);
		$this->assertEquals('FILE CONTENT', $result[0]);
	}

	/**
	 * @depends testSaveEntry
	 * @throws Exception
	 */
	public function testGetEntryViews() {
		$result = $this->fileStorage->getEntryViews($this->id);
		$this->assertIsArray($result);
		$this->assertEquals(2, sizeof($result));
		$this->assertEquals($this->views[0], $result[0]);
		$this->assertEquals($this->views[1], $result[1]);
	}

	/**
	 * @depends testSaveEntry
	 */
	public function testUpdateEntryViews() {
		$result = $this->fileStorage->updateEntryViews($this->id, 2);
		$this->assertTrue($result);
		$this->fileStorage->updateEntryViews($this->id, $this->views[0]);
	}

	/**
	 * @depends testSaveEntry
	 * @throws MissingEntry
	 */
	public function testGetEntryExpiry() {
		$result = $this->fileStorage->getEntryExpiry($this->id);
		$this->assertIsNumeric($result);
		$date = new DateTime();
		$this->assertGreaterThan($date->getTimestamp(), $result);

	}

	/**
	 * @depends testSaveEntry
	 * @throws MissingEntry
	 */
	public function testGetDelPassword() {
		$result = $this->fileStorage->getDelPassword($this->id);
		$this->assertEquals($this->deletePassword, $result);
	}


	/**
	 * @depends testSaveEntry
	 * @throws MissingEntry
	 */
	public function testGetEntryMetaData() {
		$result = $this->fileStorage->getEntryMetaData($this->id);
		$this->assertEquals($this->metadata, $result);
	}

	public function testSaveEntry() {
		$data = [
			'id' => $this->id,
			'content' => $this->content,
			'metadata' => $this->metadata
		];
		$result = $this->fileStorage->saveEntry($data, $this->deletePassword, $this->views);
		$this->assertEquals(TRUE, $result);
	}

	/**
	 * @depends testSaveEntry
	 */
	public function testListEntries() {
		$result = $this->fileStorage->listEntries();
		$this->assertContains($this->id, $result);
	}

	/**
	 * @depends testSaveEntry
	 */
	public function testEntryExists() {
		$result = $this->fileStorage->entryExists($this->id);
		$this->assertTrue($result);
	}

//	public function testDeleteEntry() {
//
//	}
}
