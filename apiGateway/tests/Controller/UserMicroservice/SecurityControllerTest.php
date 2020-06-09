<?php

namespace App\Tests\Controller\UserMicroservice;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
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
			['/api/token'        , 'PUT'],
			['/api/token'        , 'PATCH'],
			['/api/token'        , 'DELETE'],
			['/api/token'        , 'GET'],
		];
	}
}