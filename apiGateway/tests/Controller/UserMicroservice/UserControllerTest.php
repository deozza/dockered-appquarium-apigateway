<?php

namespace App\Tests\Controller\UserMicroservice;

use App\Tests\Utils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
	/**
	 * @dataProvider dataProvider_invalidMethod
	 */
	public function testInvalidMethod(string $url, string $method): void
	{
		$response = static::createClient()->request($method, $url);
		$this->assertResponseStatusCodeSame(405);
	}

	public function dataProvider_invalidMethod(): array
	{
		return [
			['/api/users'                 , 'PUT'],
			['/api/users'                 , 'GET'],
			['/api/users'                 , 'PATCH'],
			['/api/users'                 , 'DELETE'],
			['/api/users/profile'         , 'PUT'],
			['/api/users/profile'         , 'POST'],
			['/api/users/profile'         , 'DELETE'],
			['/api/users/profile/password', 'PUT'],
			['/api/users/profile/password', 'GET'],
			['/api/users/profile/password', 'POST'],
			['/api/users/profile/password', 'DELETE'],
			['/api/users/activate/token'  , 'PUT'],
			['/api/users/activate/token'  , 'GET'],
			['/api/users/activate/token'  , 'POST'],
			['/api/users/activate/token'  , 'DELETE'],
			['/api/users/list'            , 'PUT'],
			['/api/users/list'            , 'POST'],
			['/api/users/list'            , 'PATCH'],
			['/api/users/list'            , 'DELETE'],
			['/api/users/1'               , 'PUT'],
			['/api/users/1'               , 'POST'],
			['/api/users/1'               , 'DELETE'],

		];
	}

	/**
	 * @dataProvider dataprovider_needAuthentication
	 */
	public function testNeedAuthentication(string $url, string $method): void
	{
		$response = static::createClient()->request($method, $url);
		$this->assertResponseStatusCodeSame(401);
	}

	public function dataprovider_needAuthentication(): array
	{
		return [
			['/api/users/profile'         , 'GET'],
			['/api/users/profile'         , 'PATCH'],
			['/api/users/profile/password', 'PATCH'],
			['/api/users/list'            , 'GET'],
			['/api/users/1'               , 'GET'],
			['/api/users/1'               , 'PATCH'],

		];
	}

	/**
	 * @dataProvider dataprovider_forbidden
	 */
	public function testForbidden(string $url, string $method): void
	{
		$response = static::createClient()->request($method,$url, [], [], [
			'PHP_AUTH_USER' => 'standard_user',
			'PHP_AUTH_PW'   => 'password',
		]);

		$this->assertResponseStatusCodeSame(403);
	}

	public function dataprovider_forbidden(): array
	{
		return [
			['/api/users/list'            , 'GET'  ],
			['/api/users/1'               , 'GET'  ],
			['/api/users/1'               , 'PATCH'],
		];
	}
}