<?php

namespace App\Tests\Api\Communication;

use App\Api\Communication\RequestToSend;
use PHPUnit\Framework\TestCase;

class RequestToSendTest extends TestCase
{
	public function testGetCacheUrlWithoutSetCacheUrlOrCacheKey(): void
	{
		$requestToSend = new RequestToSend('url', 'method');
		$cacheUrl = $requestToSend->getCacheUrl();

		$this->assertEquals('.url', $cacheUrl);
	}

	public function testGetCacheUrlWithoutSetCacheUrl(): void
	{
		$requestToSend = new RequestToSend('url', 'method');
		$requestToSend->setCacheKey('cacheKey');
		$cacheUrl = $requestToSend->getCacheUrl();

		$this->assertEquals('cacheKey.url', $cacheUrl);
	}

	public function testGetCacheUrl(): void
	{
		$requestToSend = new RequestToSend('url', 'method');
		$requestToSend->setCacheKey('cacheKey');
		$requestToSend->setCacheUrl('cacheUrl');
		$cacheUrl = $requestToSend->getCacheUrl();

		$this->assertEquals('cacheKey.cacheUrl', $cacheUrl);
	}

	public function testCompleteObjectConstruction(): void
	{
		$requestToSend = new RequestToSend('url', 'method');
		$requestToSend->setCacheUrl('cacheUrl');
		$requestToSend->setCacheKey('cacheKey');
		$requestToSend->setBody(['body'=>'value']);
		$requestToSend->setHeaders(['header'=>'value']);
		$requestToSend->setQueryParams(['queryParam'=>'value']);

		$this->assertEquals('method', $requestToSend->getMethod());
		$this->assertEquals('url', $requestToSend->getUrl());
		$this->assertEquals(['queryParam'=>'value'], $requestToSend->getQueryParams());
		$this->assertEquals(['body'=>'value'], $requestToSend->getBody());
		$this->assertEquals(['header'=>'value'], $requestToSend->getHeaders());
		$this->assertEquals('cacheKey.cacheUrl', $requestToSend->getCacheUrl());
		$this->assertEquals('cacheKey', $requestToSend->getCacheKey());

	}
}