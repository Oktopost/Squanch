<?php
namespace Squanch\Plugins\PhpCache\Command;


use Squanch\Objects\Data;
use Squanch\Plugins\PhpCache\Connector\IPhpCacheConnector;
use Squanch\Commands\AbstractSet;
use Cache\Namespaced\NamespacedCachePool;


class Set extends AbstractSet implements IPhpCacheConnector
{
	use \Squanch\Plugins\PhpCache\Connector\TPhpCacheConnector;
	
	
	private function doSave(Data $data, NamespacedCachePool $bucket): bool
	{
		$item = $bucket->getItem($data->Id)
			->expiresAt($data->EndDate)
			->set($data->serialize());
		
		return $bucket->save($item);
	}
	
	
	protected function onInsert(Data $data): bool
	{
		$bucket = new NamespacedCachePool($this->getConnector(), $data->Bucket);
		
		if ($bucket->hasItem($data->Id))
			return false;
		
		return $this->doSave($data, $bucket);
	}

	protected function onUpdate(Data $data): bool
	{
		$bucket = new NamespacedCachePool($this->getConnector(), $data->Bucket);
		
		if (!$bucket->hasItem($data->Id))
			return false;
		
		return $this->doSave($data, $bucket);
	}

	protected function onSave(Data $data): bool
	{
		return $this->doSave($data, new NamespacedCachePool($this->getConnector(), $data->Bucket));
	}
}