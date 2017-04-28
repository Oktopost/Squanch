<?php
namespace Squanch\Plugins\InMemoryCache;


use Squanch\Objects\Data;
use Squanch\Plugins\InMemoryCache\Base\IStorage;


class Storage implements IStorage
{
	/** @var array */
	private $storage;
	
	
	public function __construct()
	{
		$this->storage = [];
	}


	public function storage(): array
	{
		return $this->storage;
	}

	public function hasBucket(string $bucket): bool
	{
		if (key_exists($bucket, $this->storage))
			return true;
		
		return false;
	}

	public function hasKey(string $bucket, string $key): bool
	{
		if (key_exists($bucket, $this->storage) && key_exists($key, $this->storage[$bucket]))
			return true;
		
		return false;
	}

	public function removeBucket(string $bucket): bool
	{
		if ($this->hasBucket($bucket))
		{
			unset($this->storage[$bucket]);
			return true;
		}
		
		return false;
	}

	public function removeKey(string $bucket, string $key): bool 
	{
		if ($this->hasKey($bucket, $key))
		{
			if (count($this->storage[$bucket]) == 1)
			{
				unset($this->storage[$bucket]);
			}
			else 
			{
				unset($this->storage[$bucket][$key]);
			}
			
			return true;
		}
		
		return false;
	}

	/**
	 * @param string $bucket
	 * @return array|null
	 */
	public function getBucketIfExists(string $bucket)
	{
		if ($this->hasBucket($bucket))
			return $this->storage[$bucket];
		
		return null;
	}

	/**
	 * @param string $bucket
	 * @param string $key
	 * @return Data|null
	 */
	public function getItemIfExists(string $bucket, string $key)
	{
		if ($this->hasKey($bucket, $key))
			return $this->storage[$bucket][$key];
		
		return null;
	}

	public function setItem(Data $item)
	{
		$this->storage[$item->Bucket][$item->Id] = $item;
	}
}