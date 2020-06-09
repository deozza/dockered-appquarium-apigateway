<?php

namespace App\Api\Communication;

use App\Api\Cache\CacheManager;
use App\Entity\User;
use Symfony\Component\HttpClient\HttpClient;

use App\Api;
use Symfony\Component\HttpFoundation\Response;

class RequestMaker
{
	private $client;
	private $cacheManager;

	public function __construct(CacheManager $cacheManager)
	{
		$this->client = HttpClient::create();
		$this->cacheManager = $cacheManager;
	}

	public function sendRequest(RequestToSend $requestToSend, ?User $user): Response
	{
		$itemFromCache = $this->preSend($requestToSend, $user);
		if(!empty($itemFromCache)) return $itemFromCache;

		$responseFromMS = $this->client->request(
			$requestToSend->getMethod(),
			$requestToSend->getUrl(), [
				'headers'=> $requestToSend->getHeaders(),
				'json'   => $requestToSend->getBody(),
				'query'  => $requestToSend->getQueryParams()
		]);

		$this->postSend($responseFromMS, $requestToSend);

		return new Response($responseFromMS->getContent(false), $responseFromMS->getStatusCode(), ['Content-Type'=>"application/json-ld"]);
	}

	private function preSend(RequestToSend $requestToSend, ?User $user): ?Response
	{
		$this->cacheManager->setCacheConfig($requestToSend);
		$itemFromCache = $this->cacheManager->getItemFromCache($requestToSend->getCacheUrl());

		if(!empty($itemFromCache))
		{
			if($this->cacheManager->hasAccess(json_decode($itemFromCache), $user))
				return new Response($itemFromCache, Response::HTTP_OK, ['Content-Type'=>"application/ld+json"]);
		}

		return null;
	}

	private function postSend($responseFromMS, RequestToSend $requestToSend): void
	{
		if($responseFromMS->getStatusCode() >= 200 && $responseFromMS->getStatusCode() < 300)
		{
			$this->cacheManager->cacheResponse($responseFromMS->getContent(), $requestToSend->getCacheUrl());
			if($requestToSend->getMethod() === "PATCH")
			{
				$this->cacheManager->invalidCache($requestToSend->getCacheUrl());
			}
		}
	}
}