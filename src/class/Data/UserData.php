<?php
namespace App\Data;

use DirectoryIterator;
use Gt\Input\InputData\Datum\FileUpload;
use Gt\WebEngine\FileSystem\Path;
use Iterator;

class UserData implements Iterator {
	protected $email;
	protected $directoryPath;
	protected $data;
	protected $dataKeys;

	protected $iteratorIndex;

	public function __construct(string $email, bool $createIfNotExists = true) {
		$this->email = $email;
		$this->directoryPath = implode(DIRECTORY_SEPARATOR, [
			Path::getDataDirectory(),
			$email,
		]);

		if(!is_dir($this->directoryPath)
		&& $createIfNotExists) {
			mkdir($this->directoryPath, 0775, true);
		}

		$this->data = $this->loadFiles();
		$this->dataKeys = array_keys($this->data);
	}

	/**
	 * @link http://php.net/manual/en/iterator.current.php
	 */
	public function current():string {
		return $this->data[$this->dataKeys[$this->iteratorIndex]];
	}

	/**
	 * @link http://php.net/manual/en/iterator.next.php
	 */
	public function next():void {
		$this->iteratorIndex++;
	}

	/**
	 * @link http://php.net/manual/en/iterator.key.php
	 */
	public function key():string {
		return $this->dataKeys[$this->iteratorIndex];
	}

	/**
	 * @link http://php.net/manual/en/iterator.valid.php
	 */
	public function valid():bool {
		return isset($this->data[$this->dataKeys[$this->iteratorIndex]]);
	}

	/**
	 * @link http://php.net/manual/en/iterator.rewind.php
	 */
	public function rewind():void {
		$this->iteratorIndex = 0;
	}

	public function set(string $key, string $value):void {
		$filePath = $this->getFilePath($key);
		$value = trim($value);

		file_put_contents($filePath, $value);
	}

	public function storePhoto(FileUpload $photo):void {
		$photo->move($this->directoryPath, "face.jpg");
	}

	public function hasPhoto():bool {
		return file_exists($this->getFilePath("face.jpg"));
	}

	public function getPhoto():?string {
		if(!$this->hasPhoto()) {
			return null;
		}

		return $this->getFilePath("face.jpg");
	}

	public function getHash():string {
		return Security::hash($this->email);
	}

	protected function getFilePath(string $file):string {
		return implode(DIRECTORY_SEPARATOR, [
			$this->directoryPath,
			$file,
		]);
	}

	protected function loadFiles():array {
		$data = [];
		if(!is_dir($this->directoryPath)) {
			return $data;
		}

		foreach(new DirectoryIterator($this->directoryPath) as $fileInfo) {
			if(!$fileInfo->isFile()) {
				continue;
			}

			$name = $fileInfo->getFilename();
			$data[$name] = file_get_contents(
				$fileInfo->getPathname()
			);
			$data[$name] = trim($data[$name]);
		}

		return $data;
	}
}