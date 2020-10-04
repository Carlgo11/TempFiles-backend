<?php

namespace com\carlgo11\tempfiles;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase {

	protected $_file; // File
	protected $_iv; // IV
	protected $_now; //DateTime
	protected $_deletionPassword;
	protected $_content;

	public function __construct($name = NULL, array $data = [], $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$this->_file = new File(NULL);

	}

	public function testSetIV() {
		global $conf;
		try {
			$iv = [Encryption::getIV($conf['Encryption-Method']), Encryption::getIV($conf['Encryption-Method'])];
		} catch (Exception $e) {
			error_log($e);
			return FALSE;
		}
		$this->assertTrue($this->_file->setIV($iv));
		return TRUE;
	}

	public function testIV() {
		$this->assertEquals($this->_iv, $this->_file->getIV());
	}

	public function testMaxViews() {
		$maxViews = 3;
		$this->assertTrue($this->_file->setMaxViews($maxViews));
		$this->assertEquals($maxViews, $this->_file->getMaxViews());

	}

	public function testSetContent() {
		$this->_content = 'ijf8z388cbbbX9GFnle45lUVw52W1Z';
		$this->assertTrue($this->_file->setContent($this->_content));
	}

	public function testGetContent() {
		$this->assertEquals($this->_content, $this->_file->getContent());
	}

//TODO: Create new tests for view functions

//    public function testCurrentViews() {
//        $currentViews = 0;
//        // FALSE = views have exceeded max views and file should be deleted.
//        $this->assertNotFalse($this->_file->setCurrentViews($currentViews));
//        $this->assertEquals($currentViews, $this->_file->getCurrentViews());
//    }


//    public function testMetaData() {
//
//    }
//
//

	public function testGetID() {
		$this->assertNotNull($this->_file->getID());

	}

	public function testSetDateTime() {
		$this->_now = new DateTime();
		$this->assertTrue($this->_file->setDateTime($this->_now));
	}

	public function testGetDateTime() {
		$this->assertEquals($this->_file->getDateTime(), $this->_now);
	}

	public function testSetDeletionPassword() {
		$this->_deletionPassword = "27DTaEw1eK1rmJ63RKjsq8N1Sp8Mm4";
		$this->assertTrue($this->_file->setDeletionPassword($this->_deletionPassword));
	}

	public function testGetDeletionPassword() {
		$this->assertEquals($this->_file->getDeletionPassword(), $this->_deletionPassword);
	}

}
