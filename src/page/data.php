<?php
namespace App\Page;

use App\Data\Security;
use DirectoryIterator;
use Gt\WebEngine\FileSystem\Path;
use Gt\WebEngine\Logic\Page;
use stdClass;

class DataPage extends Page {
	public function go() {
		$obj = new StdClass();
		$obj->userList = [];

		foreach(new DirectoryIterator(Path::getDataDirectory()) as $file) {
			if($file->isDot()) {
				continue;
			}

			$fileName = $file->getFilename();
			if(!strstr($fileName, "@")) {
				continue;
			}

			$user = new StdClass();
			$user->id = Security::hash($fileName);

			foreach(new DirectoryIterator($file->getPathname()) as $inFile) {
				if(!$inFile->isFile()) {
					continue;
				}

				$key = $inFile->getFilename();
				$key = strtok($key, ".");

				if($inFile->getExtension() === "jpg") {
					$value = implode("/", [
						$this->serverInfo->getRemoteHost(),
						"photo?id=" . $user->id
					]);
				}
				else {
					$value = file_get_contents($inFile->getPathname());
				}

				if(strlen($value) === 0) {
					$value = null;
				}

				$user->$key = $value;
			}

			$obj->userList []= $user;
		}

		header("Content-type: application/json");
		echo json_encode($obj);
		exit;
	}
}