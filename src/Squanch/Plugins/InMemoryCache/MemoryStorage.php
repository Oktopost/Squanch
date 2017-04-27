<?php
namespace Squanch\Plugins\InMemoryCache;


use Squanch\Objects\Data;


class MemoryStorage
{
	private $storage;
	
	
	public function __construct()
	{
		$this->storage = new \stdClass();
	}


	public function getStorage(): \stdClass
	{
		return $this->storage;
	}
	
	public function hasBucket(string $bucket): bool
	{
		return isset($this->storage->$bucket);
	}
	
	public function createBucket(string $bucket)
	{
		if (!$this->hasBucket($bucket))
			$this->storage->$bucket = [];
	}
	
	public function hasItem(string $bucket, string $key): bool
	{
		if (!$this->hasBucket($bucket))
			return false;
		
		$bucket = $this->storage->$bucket;
		
		return isset($bucket[$key]);
	}
	
	public function set(Data $data)
	{
		$this->createBucket($data->Bucket);
		$bucket = $this->storage->{$data->Bucket};
		$bucket[$data->Id] = $data;
	}
	
	public function delete(string $bucket, string $key)
	{
		if (!$this->hasItem($bucket, $key))
			return false;
		
		$bucket = $this->storage->{$bucket};
		unset($bucket[$key]);
	}
}