<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Commands\AbstractGet;

use Squanch\Plugins\Predis\Utils\UpdateTTL;
use Squanch\Plugins\Predis\Connector\IPredisConnector;


class Get extends AbstractGet implements IPredisConnector
{
	use \Squanch\Plugins\Predis\Connector\TPredisConnector;
	
	
	private function getFullKey(CallbackData $data)
	{
		return "{$data->Bucket}:{$data->Key}";
	}
	
	
	protected function onUpdateTTL(CallbackData $data, int $ttl)
	{
		$updater = new UpdateTTL($this->getClient());
		$updater->updateTTL($data, $ttl);
	}
	
	/**
	 * @param CallbackData $data
	 * @return Data|null
	 */
	protected function onGet(CallbackData $data)
	{
		$key = $this->getFullKey($data);
		
		$item = $this->getClient()->hgetall($key);
		
		if (!$item)
			return null;
			
		$item['TTL'] = $this->getClient()->ttl($key);
		
		return Data::deserialize($item);
	}
}