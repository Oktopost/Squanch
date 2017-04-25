<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Objects\Data;
use Squanch\Commands\AbstractSet;
use Predis\Client;


/**
 * @method Client getConnector()
 */
class Set extends AbstractSet
{
	private function getFullKey(Data $data)
	{
		return "{$data->Bucket}:{$data->Id}";
	}


	/**
	 * @param Data $data
	 * @param bool|null $isExists
	 * @return bool
	 */
	private function saveOnCondition(Data $data, $isExists): bool
	{
		$connector = $this->getConnector();
		$key = $this->getFullKey($data);
		
		if (!is_null($isExists) && $isExists != $connector->exists($key))
			return false;
		
		$connector->hmset($key, $data->serializeToArray());
		$connector->expire($key, $data->TTL);
		
		return true;
	}
	
	
	protected function onInsert(Data $data): bool
	{
		return $this->saveOnCondition($data, false);
	}

	protected function onUpdate(Data $data): bool
	{
		return $this->saveOnCondition($data, true);
	}

	protected function onSave(Data $data): bool
	{
		return $this->saveOnCondition($data, null);
	}
}