<?php

namespace com\carlgo11\tempfiles;

use DateTime;

class FileStorage
{
	/**
	 * Get File deletion time
	 *
	 * @param string $id ID of the file.
	 * @return false|string
	 */
	public function getFileDeletionTime($id) {
		global $conf;
		$file = fopen($conf['file-path'] . $id, "r");
		$json = json_decode($file);
		return base64_decode($json['time']);
	}

	/**
	 * Save file to storage.
	 *
	 * @param File $file
	 * @param string $password
	 * @throws \Exception
	 */
	public function saveFile(File $file, string $password) {
		global $conf;
		$content = [];
		$newFile = fopen($conf['file-path'] . $file->getID(), "w");

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
		fwrite($newFile, $txt);
		fclose($newFile);
	}

	/**
	 * Get File from storage
	 *
	 * @param string $id ID of the file.
	 * @param string $password Password of the file.
	 * @return False|File Returns the saved file as a File object.
	 */
	public function getFile(string $id, string $password) {
		global $conf;
		$plaintext = fopen($conf['file-path'] . $id, "r");
		$json = json_decode($plaintext);
		$file = new File(NULL);

		$iv_array = explode(" ", base64_decode($json['iv']));
		$file->setIV([
			'content_iv' => base64_decode($iv_array[0]),
			'content_tag' => base64_decode($iv_array[1]),
			'metadata_iv' => base64_decode($iv_array[2]),
			'metadata_tag' => base64_decode($iv_array[3])
		]);

		$content = Encryption::decrypt(base64_decode($json['content']), $password, $file->getIV('content_iv'), $file->getIV('content_tag'));
		$metadata_string = Encryption::decrypt(base64_decode($json['metadata']), $password, $file->getIV('metadata_iv'), $file->getIV('metadata_tag'));

		if ($metadata_string === FALSE) return FALSE;

		$metadata_array = explode(' ', $metadata_string);
		$metadata = ['name' => $metadata_array[0], 'size' => $metadata_array[1], 'type' => $metadata_array[2]];
		$views_array = explode(' ', base64_decode($metadata_array[4]));

		$file->setContent($content);
		$file->setMetaData($metadata);
		$file->setCurrentViews((int)$views_array[0]);
		$file->setMaxViews((int)$views_array[1]);
		$file->setDeletionPassword(base64_decode($metadata_array[3]));

		return $file;
	}
}
