<?php

namespace App\Api\Communication;


class RequestToSend
{
	private $method;
	private $url;
	private $queryParams;
	private $body;
	private $headers;
	private $cacheUrl;
	private $cacheKey;

	public function __construct(string $url, string $method)
	{
		$this->url = $url;
		$this->method = $method;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function setMethod(string $method): self
	{
		$this->method = $method;
		return $this;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function setUrl(string $url): self
	{
		$this->url = $url;
		return $this;
	}

	public function getQueryParams(): ?array
	{
		return $this->queryParams;
	}

	public function setQueryParams(array $queryParams): self
	{
		$this->queryParams = $queryParams;
		return $this;
	}

	public function getBody(): ?array
	{
		return $this->body;
	}

	public function setBody(?array $body): self
	{
		$this->body = $body;
		return $this;
	}

	public function getHeaders(): ?array
	{
		return $this->headers;
	}

	public function setHeaders(array $headers): self
	{
		$this->headers = $headers;
		return $this;
	}

	public function getCacheUrl(): string
	{
		$completeCacheUrl = $this->getCacheKey();
		if(!empty($this->cacheUrl)) return $completeCacheUrl.'.'.$this->cacheUrl;

		return $completeCacheUrl.'.'.$this->getUrl();
	}

	public function setCacheUrl(string $cacheUrl): self
	{
		$this->cacheUrl = $cacheUrl;
		return $this;
	}

	public function getCacheKey(): ?string
	{
		return $this->cacheKey;
	}

	public function setCacheKey(string $cacheKey): self
	{
		$this->cacheKey = $cacheKey;
		return $this;
	}
}