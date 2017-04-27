<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Objects\Data;
use Squanch\Plugins\Predis\Connector\IPredisConnector;
use Squanch\Commands\AbstractSet;


class Set extends AbstractSet implements IPredisConnector
{
	use \Squanch\Plugins\Predis\Connector\TPredisConnector;
	
	
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
		$connector = $this->getClient();
		$key = $this->getFullKey($data);
		
		if (!is_null($isExists) && $isExists != $connector->exists($key))
			return false;
		
		$connector->hmset($key, $data->serializeToArray());
		
		if ($data->TTL < 0)
		{
			$connector->persist($key);
		}
		else
		{
			$connector->expire($key, $data->TTL);
		}
		
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