<?php
namespace App\Data;

class Security {
	const SECRET = "hack24 super secret password";

	public static function hash(string $input):string {
		// TODO: Make this actually secure using openssl_encrypt
		return base64_encode($input);
	}

	public static function unhash(string $hash):string {
		return base64_decode($hash);
	}
}