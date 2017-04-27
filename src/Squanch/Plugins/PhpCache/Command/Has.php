<?php
namespace Squanch\Plugins\PhpCache\Command;


use Squanch\Objects\CallbackData;
use Squanch\Plugins\PhpCache\Connector\IPhpCacheConnector;
use Squanch\Commands\AbstractHas;
use Cache\Namespaced\NamespacedCachePool;


class Has extends AbstractHas implements IPhpCacheConnector
{
	use \Squanch\Plugins\PhpCache\Connector\TPhpCacheConnector;
	
	
	protected function onCheck(CallbackData $data): bool
	{
		$bucket = new NamespacedCachePool($this->getConnector(), $data->Bucket);
		return $bucket->hasItem($data->Key);
	}

	protected function onUpdateTTL(CallbackData $data, int $ttl): bool
	{
		$bucket = new NamespacedCachePool($this->getConnector(), $data->Bucket);
		$bucket->getItem($data->Key)->expiresAfter($ttl);
		return true;
	}
}