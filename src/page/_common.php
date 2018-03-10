<?php
namespace App\Page;

use Gt\WebEngine\Logic\Page;

class _CommonPage extends Page {
	public function go() {
		$this->checkAuthenticated();
	}

	protected function checkAuthenticated():void {
		$path = $this->serverInfo->getRequestUri()->getPath();
		if($path === "/"
		|| $path === "/photo"
		|| $path === "/data") {
			return;
		}

		if(!$this->session->has("auth")) {
			header("Location: /");
			exit;
		}
	}
}