<?php


namespace App\Api\Cache;

use App\Api\Communication\RequestToSend;
use App\Entity\User;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Yaml\Yaml;

class CacheManager
{
	private $cacheConfig;
	private $cache;

	public function __construct(AdapterInterface $cache)
	{
		$this->cache = $cache;
	}

	public function setCacheConfig(RequestToSend $requestToSend)
	{
		if(empty($requestToSend)) return;

		$config = Yaml::parse(file_get_contents(__DIR__.'/cacheConfig.yaml'));

		if(!array_key_exists($requestToSend->getMethod(), $config)) return;
		$config = $config[$requestToSend->getMethod()];

		if(!array_key_exists($requestToSend->getCacheKey(), $config)) return;

		$this->cacheConfig = $config[$requestToSend->getCacheKey()];
	}

	public function getItemFromCache(string $cacheName): ?string
	{
		$item = $this->cache->getItem(urlencode($cacheName));
		if(!$item->isHit()) return null;

		return $item->get();
	}

	public function hasAccess(object $itemFromCache, ?User $user): bool
	{
		if(array_key_exists('mustBeOwner',$this->cacheConfig) && $this->cacheConfig['mustBeOwner'] == true)
		{
			if(empty($user)) return false;

			if(!property_exists($itemFromCache, 'owner')) return $user->getId() == $itemFromCache->{'@id'};

			return $user->getId() == $itemFromCache->owner;
		}

		if(array_key_exists('mustHaveRoles',$this->cacheConfig))
		{
			foreach($this->cacheConfig['mustHaveRoles'] as $role)
			{
				if(!in_array($role, $user->getRoles())) return false;
			}
		}

		return true;
	}

	public function cacheResponse(string $content, string $cacheName)
	{
		if(empty($this->cacheConfig)) return;

		$item = $this->cache->getItem(urlencode($cacheName));
		if (!$item->isHit()) {
			$item->set($content);
			$item->expiresAfter($this->cacheConfig['expiresAfter']);
			$this->cache->save($item);
		}
	}

	public function invalidCache(string $cacheName)
	{
		if(empty($this->cacheConfig)) return;

		$item = $this->cache->getItem(urlencode($cacheName));
		if (!$item->isHit()) {
			$this->cache->deleteItem($cacheName);
		}
	}
}