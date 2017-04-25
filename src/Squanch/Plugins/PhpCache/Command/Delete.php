<?php
namespace Squanch\Plugins\PhpCache\Command;


use Squanch\Commands\AbstractDelete;
use Cache\Namespaced\NamespacedCachePool;


/**
 * @method \Cache\Hierarchy\HierarchicalPoolInterface getConnector()
 */
class Delete extends AbstractDelete
{
	protected function onDeleteBucket(string $bucket): bool
	{
		$bucket = new NamespacedCachePool($this->getConnector(), $bucket);
		return $bucket->clear();
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		$bucket = new NamespacedCachePool($this->getConnector(), $bucket);
		return $bucket->deleteItem($key);
	}
}