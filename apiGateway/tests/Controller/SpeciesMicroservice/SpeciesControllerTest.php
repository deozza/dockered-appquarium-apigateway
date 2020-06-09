<?php

namespace App\Tests\Controller\SpeciesMicroservice;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SpeciesControllerTest extends WebTestCase
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
			['/api/species'  , 'PUT'],
			['/api/species'  , 'PATCH'],
			['/api/species'  , 'DELETE'],
			['/api/species/1', 'PUT'],
			['/api/species/1', 'POST'],
			['/api/species/1', 'DELETE'],
		];
	}
}