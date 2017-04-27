<?php
namespace Squanch\Plugins\PhpCache\Command;


use Squanch\Commands\AbstractDelete;
use Cache\Namespaced\NamespacedCachePool;
use Squanch\Plugins\PhpCache\Connector\IPhpCacheConnector;


class Delete extends AbstractDelete implements IPhpCacheConnector
{
	use \Squanch\Plugins\PhpCache\Connector\TPhpCacheConnector;
	
	
	protected function onDeleteBucket(string $bucket): bool
	{
		$bucket = new NamespacedCachePool($this->getConnector(), $bucket);
		var_dump($bucket);
		return $bucket->clear();
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		$bucket = new NamespacedCachePool($this->getConnector(), $bucket);
		return $bucket->deleteItem($key);
	}
}