<?php
namespace Squanch\Plugins\InMemoryCache\Command;


use Squanch\Commands\AbstractDelete;
use Squanch\Plugins\InMemoryCache\Base\IStorage;


class Delete extends AbstractDelete
{
	/** @var IStorage */
	private $storage;
	
	
	public function __construct(IStorage $storage)
	{
		$this->storage = $storage;
	}


	protected function onDeleteBucket(string $bucket): bool
	{
		return $this->storage->removeBucket($bucket);
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		return $this->storage->removeKey($bucket, $key);
	}
}