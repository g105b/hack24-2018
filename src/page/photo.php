<?php
namespace App\Page;

use App\Data\Security;
use App\Data\UserData;
use Gt\Input\InputData\InputData;
use Gt\WebEngine\Logic\Page;
use stdClass;

class PhotoPage extends Page {
	public function go() {
		if($this->input->has("id")) {
			$this->displayPhoto($this->input->get("id"));
		}
	}

	public function displayPhoto(string $id) {
		$email = Security::unhash($id);
		$userData = new UserData($email, false);
		$photo = $userData->getPhoto();

		header("Content-type: image/jpeg");
		echo readfile($photo);
		exit;
	}
}