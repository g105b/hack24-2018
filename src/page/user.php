<?php
namespace App\Page;

use App\Data\UserData;
use Gt\Input\InputData\Datum\FileUpload;
use Gt\Input\InputData\InputData;

class UserPage extends \Gt\WebEngine\Logic\Page {
	const SKIP_SAVE_AS_TEXT_FIELDS = ["do", "face"];

	public function go() {
		$this->input->do("save")
			->call([$this, "save"]);
		$this->input->do("logout")
			->call([$this, "logout"]);

		$this->outputData();
	}

	public function logout() {
		$this->session->delete("auth");
		header("Location: /");
		exit;
	}

	public function save(InputData $data) {
		$userData = new UserData($this->session->get("auth"));

		/** @var FileUpload $photo */
		$photo = $data->get("face")[0];
		$fileName = $photo->getOriginalName();
		if(!empty($fileName)) {
			$userData->storePhoto($photo);
		}

		foreach($data as $key => $value) {
			if(in_array($key, self::SKIP_SAVE_AS_TEXT_FIELDS)) {
				continue;
			}

			$userData->set($key, $value);
		}

		header("Location: /user");
		exit;
	}

	protected function outputData() {
		$userData = new UserData($this->session->get("auth"));

		foreach($userData as $key => $value) {
			$element = $this->document->querySelector("[name='$key']");
			if(!$element) {
				continue;
			}

			switch($element->tagName) {
			case "textarea":
				$element->innerHTML = $value;
				break;

			default:
				$element->value = $value;
				break;
			}
		}

		if($userData->hasPhoto()) {
			$hash = $userData->getHash();
			$photoElement = $this->document->getElementById("face-photo");
			$photoElement->setAttribute("src", "/photo?id=$hash");
		}
	}
}