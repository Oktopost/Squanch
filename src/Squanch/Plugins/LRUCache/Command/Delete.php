<?php
namespace Squanch\Plugins\LRUCache\Command;


use Squanch\Commands\AbstractDelete;
use Squanch\Plugins\LRUCache\Base\ILRUAdapter;


class Delete extends AbstractDelete
{
	/** @var ILRUAdapter */
	private $lruAdapter;
	
	
	protected function onDeleteBucket(string $bucket): bool
	{
		return $this->lruAdapter->removeBucket($bucket);
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		return $this->lruAdapter->removeKey($bucket, $key);
	}

	
	public function __construct(ILRUAdapter $lruAdapter)
	{
		$this->lruAdapter = $lruAdapter;
	}
}