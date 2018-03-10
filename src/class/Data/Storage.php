<?php
namespace App\Data;

use Gt\WebEngine\FileSystem\Path;

class Storage {
	protected $path;
	protected $name;
	protected $data;

	public function __construct(string $name) {
		$this->path = implode(DIRECTORY_SEPARATOR, [
			Path::getDataDirectory(),
			$name . ".csv",
		]);
		$this->name = $name;

		$this->data = $this->loadCsv() ?? [];
	}

	public function fieldHasValue(string $fieldName, string $fieldValue):bool {
		foreach($this->data as $row) {
			if($row[$fieldName] === $fieldValue) {
				return true;
			}
		}

		return false;
	}

	protected function loadCsv():?array {
		if(!is_file($this->path)) {
			return null;
		}

		$fh = fopen($this->path, "r");
		$headers = null;
		$data = [];

		while(false !== ($row = fgetcsv($fh)) ) {
			if(is_null($headers)) {
				$headers = $row;
				continue;
			}

			$dataRow = [];

			foreach($row as $i => $datum) {
				$dataRow[$headers[$i]] = $datum;
			}

			$data []= $dataRow;
		}

		fclose($fh);
		return $data;
	}

	public function add(array $data):void {
		$fh = fopen($this->path, "a");
		fputcsv($fh, [
			$data["email"],
			$data["password"],
		]);
		fclose($fh);
	}

	public function getBy(string $key, string $value):?array {
		foreach($this->data as $row) {
			if($row[$key] === $value) {
				return $row;
			}
		}

		return null;
	}
}