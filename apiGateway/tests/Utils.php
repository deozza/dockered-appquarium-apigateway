<?php


namespace App\Tests;


use Firebase\JWT\JWT;

class Utils
{
	static public function generateToken(string $userId, string $tokenExpire, string $tokenKind, array $roles = [], string $signature = null): string
	{
		if(empty($signature))
			$signature = $_ENV['APP_SECRET'];

		$validToken = JWT::encode([
			'id' => '/api/users/'.$userId,
			'exp' => date_create($tokenExpire)->format('U'),
			"roles"=>array_merge($roles, ['USER_ROLE']),
			'kind'=>$tokenKind
		], $signature);

		return $validToken;
	}
}