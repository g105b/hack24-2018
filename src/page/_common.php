<?php
namespace App\Page;

use Gt\WebEngine\Logic\Page;

class _CommonPage extends Page {
	const NO_AUTH_PATHS = [
		"/",
		"/photo",
		"/data",
		"/signup",
	];

	public function go() {
		$this->checkAuthenticated();
	}

	protected function checkAuthenticated():void {
		$path = $this->serverInfo->getRequestUri()->getPath();
		if(in_array($path, self::NO_AUTH_PATHS)) {
			return;
		}

		if(!$this->session->has("auth")) {
			header("Location: /");
			exit;
		}
	}
}