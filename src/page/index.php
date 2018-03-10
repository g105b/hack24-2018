<?php
namespace App\Page;

use App\Auth\Authentication;
use App\Auth\InvalidAuthenticationException;
use Gt\Input\InputData\InputData;

class IndexPage extends \Gt\WebEngine\Logic\Page {
	public function go() {
		$this->input->do("login")
			->call([$this, "login"]);
	}

	public function login(InputData $data) {
		try {
			Authentication::login(
				$data["email"],
				$data["password"]
			);
			$this->session("auth", $data["email"]);
			header("Location: /user");
			exit;
		}
		catch(InvalidAuthenticationException $exception) {
			// TODO: Better error output.
			die("Invalid authentication details");
		}
	}
}