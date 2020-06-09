<?php

namespace App\Tests\Api\Communication;

use App\Api\Cache\CacheManager;
use App\Api\Communication\RequestMaker;
use App\Api\Communication\RequestToSend;
use App\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class RequestMakerTest extends TestCase
{

	private function getMockedCacheManagerForPreSend(
		bool $setCacheConfigIsCalled = false,
		bool $itemFromCacheIsCalled = false,
		bool $itemFromCacheIsFound = false,
		bool $hasAccessIsCalled = false,
		bool $hasAccess = false): MockObject
	{
		$mockedCacheManager = $this->createMock(CacheManager::class);

		if($setCacheConfigIsCalled)
			$mockedCacheManager->expects($this->once())->method('setCacheConfig');

		if($itemFromCacheIsCalled)
			$mockedCacheManager->expects($this->once())->method('getItemFromCache')->willReturn($itemFromCacheIsFound ? '{"item": "value"}' : null);

		if($hasAccessIsCalled)
		{
			$mockedCacheManager->expects($this->once())
				->method('hasAccess')
				->willReturn($hasAccess);
		}

		return $mockedCacheManager;
	}

	private function getMockedCacheManagerForPostSend(
		bool $cacheResponseIsCalled = false,
		bool $invalidCacheIsCalled = false): MockObject
	{
		$mockedCacheManager = $this->createMock(CacheManager::class);

		if($cacheResponseIsCalled)
			$mockedCacheManager->expects($this->once())->method('cacheResponse');

		if($invalidCacheIsCalled)
			$mockedCacheManager->expects($this->once())->method('invalidCache');


		return $mockedCacheManager;
	}

	private function getMockedResponseFromMS(int $statusCode, bool $getContentIsCalled = false): MockObject
	{
		$mockedResponseFromMS = $this->createMock(Response::class);
		$mockedResponseFromMS->expects($this->exactly(2))->method('getStatusCode')->willReturn($statusCode);

		if($getContentIsCalled)
			$mockedResponseFromMS->expects($this->once())->method('getContent')->willReturn('');

		return $mockedResponseFromMS;
	}

	public function testPreSendWithNoCacheFound(): void
	{
		$requestMaker = new RequestMaker($this->getMockedCacheManagerForPreSend(true, true, false));
		$requestToSend = new RequestToSend('url', 'method');
		$user = new User([
			'@id'=>'id',
			'roles' => ['roles'],
			'username' => 'username'
		], 'token');
		$reflectedRequestMaker = new \ReflectionObject($requestMaker);
		$preSendMethod = $reflectedRequestMaker->getMethod('preSend');
		$preSendMethod->setAccessible(true);

		$response = $preSendMethod->invoke($requestMaker, $requestToSend, $user);
		$this->assertEmpty($response);
	}

	public function testPreSendWithCacheFoundAndNoAccess(): void
	{
		$requestMaker = new RequestMaker($this->getMockedCacheManagerForPreSend(true, true, true, true, false));
		$requestToSend = new RequestToSend('url', 'method');
		$user = new User([
			'@id'=>'id',
			'roles' => ['roles'],
			'username' => 'username'
		], 'token');
		$reflectedRequestMaker = new \ReflectionObject($requestMaker);
		$preSendMethod = $reflectedRequestMaker->getMethod('preSend');
		$preSendMethod->setAccessible(true);

		$response = $preSendMethod->invoke($requestMaker, $requestToSend, $user);
		$this->assertEmpty($response);
	}

	public function testPreSendWithCacheFoundAndAccess(): void
	{
		$requestMaker = new RequestMaker($this->getMockedCacheManagerForPreSend(true, true, true, true, true));
		$requestToSend = new RequestToSend('url', 'method');
		$user = new User([
			'@id'=>'id',
			'roles' => ['roles'],
			'username' => 'username'
		], 'token');
		$reflectedRequestMaker = new \ReflectionObject($requestMaker);
		$preSendMethod = $reflectedRequestMaker->getMethod('preSend');
		$preSendMethod->setAccessible(true);
		$expectedResponse = new Response('{"item": "value"}', Response::HTTP_OK, ['Content-Type'=>'application/ld+json']);

		$response = $preSendMethod->invoke($requestMaker, $requestToSend, $user);

		$this->assertEquals($expectedResponse, $response);
	}

	public function testPostSendOnApiError(): void
	{
		$requestMaker = new RequestMaker($this->getMockedCacheManagerForPostSend());
		$requestToSend = new RequestToSend('url', 'method');
		$reflectedRequestMaker = new \ReflectionObject($requestMaker);
		$postSendMethod = $reflectedRequestMaker->getMethod('postSend');
		$postSendMethod->setAccessible(true);
		$mockedResponseFromMS = $this->getMockedResponseFromMS(400);

		$postSendMethod->invoke($requestMaker, $mockedResponseFromMS, $requestToSend);
	}

	public function testPostSendOnApiSuccess(): void
	{
		$requestMaker = new RequestMaker($this->getMockedCacheManagerForPostSend(true));
		$requestToSend = new RequestToSend('url', 'method');
		$reflectedRequestMaker = new \ReflectionObject($requestMaker);
		$postSendMethod = $reflectedRequestMaker->getMethod('postSend');
		$postSendMethod->setAccessible(true);
		$mockedResponseFromMS = $this->getMockedResponseFromMS(201, true);

		$postSendMethod->invoke($requestMaker, $mockedResponseFromMS, $requestToSend);
	}

	public function testPostSendOnApiPatchSuccess(): void
	{
		$requestMaker = new RequestMaker($this->getMockedCacheManagerForPostSend(true, true));
		$requestToSend = new RequestToSend('url', 'PATCH');
		$reflectedRequestMaker = new \ReflectionObject($requestMaker);
		$postSendMethod = $reflectedRequestMaker->getMethod('postSend');
		$postSendMethod->setAccessible(true);
		$mockedResponseFromMS = $this->getMockedResponseFromMS(201, true);

		$postSendMethod->invoke($requestMaker, $mockedResponseFromMS, $requestToSend);
	}
}