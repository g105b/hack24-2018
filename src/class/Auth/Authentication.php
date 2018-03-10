<?php
namespace App\Auth;

use App\Data\Storage;

class Authentication {
	public static function exists(string $email):bool {
		$storage = new Storage("auth");
		return $storage->fieldHasValue("email", $email);
	}

	public static function create(
		string $email,
		string $password
	):void {
		$storage = new Storage("auth");
		$storage->add([
			"email" => $email,
			"password" => $password,
		]);
	}

	public static function checkPasswordsMatch(string $password, string $password2) {
		if($password !== $password2) {
			throw new PasswordsDoNotMatchException();
		}
	}

	public static function hashPassword(string $password):string {
		return password_hash($password, PASSWORD_BCRYPT);
	}
}