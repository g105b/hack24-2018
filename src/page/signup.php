<?php
namespace App\Page;

use App\Auth\Authentication;
use App\Auth\UserAlreadyExistsException;
use Gt\Input\InputData\InputData;

class SignupPage extends \Gt\WebEngine\Logic\Page {
	public function go() {
		$this->input->do("signup")
			->call([$this, "signup"]);
	}

	public function signup(InputData $data) {
		if(Authentication::exists($data["email"])) {
			throw new UserAlreadyExistsException($data["email"]);
		}

		Authentication::checkPasswordsMatch(
			$data["password"],
			$data["password2"]
		);
		$passHash = Authentication::hashPassword($data["password"]);
		Authentication::create($data["email"], $passHash);
		$this->session->set("auth", $data["email"]);
		header("Location: /user");
		exit;
	}
}