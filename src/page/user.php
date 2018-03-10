<?php
namespace App\Page;

class UserPage extends \Gt\WebEngine\Logic\Page {
	public function go() {
		$this->input->do("logout")
			->call([$this, "logout"]);
	}

	public function logout() {
		$this->session->delete("auth");
		header("Location: /");
		exit;
	}
}