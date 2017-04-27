<?php
namespace Squanch\Plugins\InMemoryCache\Command;


use Squanch\Commands\AbstractDelete;


class Delete extends AbstractDelete
{
	/** @var \stdClass */
	private $storage;
	
	
	public function __construct(\stdClass $storage)
	{
		$this->storage = $storage;
	}


	protected function onDeleteBucket(string $bucket): bool
	{
		if (!isset($this->storage->$bucket))
			return false;
		
		unset($this->storage->$bucket);
		return true;
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		if (!isset($this->storage->$bucket) || isset($this->storage->$bucket->$key))
			return false;
		
		unset($this->storage->$bucket->$key);
		
		return true;
	}
}