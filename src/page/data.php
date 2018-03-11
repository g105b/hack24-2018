<?php
namespace App\Page;

use App\Data\Security;
use DirectoryIterator;
use Gt\WebEngine\FileSystem\Path;
use Gt\WebEngine\Logic\Page;
use stdClass;

class DataPage extends Page {
	public function go() {
		$userTeamsJson = file_get_contents(implode(DIRECTORY_SEPARATOR, [
			Path::getDataDirectory(),
			"user_teams.json",
		]));

		$userTeams = json_decode($userTeamsJson);

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

			$slackTeam = null;
			$slackMotto = null;

			foreach(new DirectoryIterator($file->getPathname()) as $inFile) {
				if(!$inFile->isFile()) {
					continue;
				}

				$key = $inFile->getFilename();
				$key = strtok($key, ".");

				if($inFile->getExtension() === "jpg") {
					$value = implode("/", [
						"https://hack24-2018.g105b.com",
						"photo?id=" . $user->id
					]);
				}
				else {
					$value = file_get_contents($inFile->getPathname());
				}

				if(strlen($value) === 0) {
					$value = null;
				}

				$value = trim($value);

				if($key === "slack") {
					$slackTeam = $userTeams->$value->name;
					$slackMotto = $userTeams->$value->motto;
				}

				$user->$key = $value;
			}

			$user->slackTeam = $slackTeam;
			$user->slackMotto = $slackMotto;

			$obj->userList []= $user;
		}

		header("Content-type: application/json");
		echo json_encode($obj);
		exit;
	}
}