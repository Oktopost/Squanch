<?php
namespace Squanch\Plugins\LRUCache;


use LRUCache\LRUCache;
use Squanch\Objects\Data;
use Squanch\Plugins\LRUCache\Base\ILRUAdapter;
use Squanch\Exceptions\OperationNotSupportedOnBucketException;


class LRUAdapter implements ILRUAdapter
{
	/** @var LRUCache */
	private $lruCache;
	
	
	private function isOldItem(Data $item): bool
	{
		if ($item->EndDate->getTimestamp() < time())
		{
			return true;
		}
		
		return false;
	}
	
	private function getCompositeKey(string $bucket, string $key): string 
	{
		return "$bucket:$key";
	}	
	
	
	public function __construct($capacity)
	{
		if ($capacity == null)
		{
			throw new \Exception('Capacity must be set');
		}
		
		$this->lruCache = new LRUCache($capacity);
	}

	
	public function hasBucket(string $bucket): bool
	{
		throw new OperationNotSupportedOnBucketException('Has Bucket');
	}

	public function hasKey(string $bucket, string $key): bool
	{
		return !is_null($this->lruCache->get($this->getCompositeKey($bucket, $key)));
	}

	public function removeBucket(string $bucket): bool
	{
		throw new OperationNotSupportedOnBucketException('Remove Bucket');
	}

	public function removeKey(string $bucket, string $key): bool
	{
		return $this->lruCache->remove($this->getCompositeKey($bucket, $key));
	}

	public function getItemIfExists(string $bucket, string $key)
	{
		/** @var Data $item */
		$item = $this->lruCache->get($this->getCompositeKey($bucket, $key));
		
		if ($item != null)
		{
			if ($this->isOldItem($item))
			{
				$this->removeKey($item->Bucket, $item->Id);
				$item = null;
			}
			
			return $item;
		}
		
		return null;
	}

	public function setItem(Data $item): bool
	{
		return $this->lruCache->put($this->getCompositeKey($item->Bucket, $item->Id), $item);
	}
	
	public function createItem(Data $item): bool
	{
		if ($this->hasKey($item->Bucket, $item->Id))
			return false;
		
		return $this->setItem($item);
	}
	
	public function updateItem(Data $item): bool
	{
		if (!$this->hasKey($item->Bucket, $item->Id))
			return false;
		
		return $this->setItem($item);
	}
	
	public function setTTL(string $bucket, string $key, int $ttl)
	{
		$item = $this->getItemIfExists($bucket, $key);
		
		if ($item != null)
		{
			$item->setTTL($ttl);
		}
	}
}