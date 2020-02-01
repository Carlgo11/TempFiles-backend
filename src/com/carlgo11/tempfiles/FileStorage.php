<?php


namespace com\carlgo11\tempfiles\api;


use com\carlgo11\tempfiles\Encryption;
use com\carlgo11\tempfiles\File;
use DateTime;

class FileStorage
{
	public function __construct()
	{

	}

	private function getMetaData($id)
	{

	}

	private function getFileData($id)
	{

	}

	private function getFileDeletionTime()
	{

	}

	/**
	 * Save file to storage.
	 * @param File $file
	 * @param string $password
	 * @throws \Exception
	 */
	public function saveFile(File $file, string $password)
	{
		$newfile = fopen("/tmp/tempfiles/".$file->getID(), "w");
		$content = [];
		$fileContent = Encryption::encryptFileContent($file->getContent(), $password);
		$fileMetadata = Encryption::encryptFileDetails($file->getMetaData(), $file->getDeletionPassword(), 0, $file->getMaxViews(), $password);
		$iv = [$fileContent['iv'], $fileContent['tag'], $fileMetadata['iv'], $fileMetadata['tag']];
		$date = new DateTime();
		$time = $date->getTimestamp();

		$content['time'] = $time;
		$content['metadata'] = $fileMetadata['data'];
		$content['iv'] = base64_encode(implode(' ', $iv));;
		$content['content'] = $fileContent['data'];

		$txt = json_encode($content, JSON_PRETTY_PRINT);
		fwrite($newfile, $txt);
		fclose($newfile);
	}

	/**
	 * Get File from storage
	 * @param string $id ID of the file.
	 * @return File Returns the saved file as a File object.
	 */
	private function getFile(string $id)
	{
		$plaintext = fopen("/tmp/tempfiles/".$id, "r");
		$json = json_decode($plaintext);
		$file = new File();
		$file->setContent($json['content']);
		$file->setMetaData($json['metadata']);
		$file->setIV($json['iv']);
		return $file;
	}
}
