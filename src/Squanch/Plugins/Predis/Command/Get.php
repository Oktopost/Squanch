<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Commands\AbstractGet;
use Squanch\Plugins\Predis\Connector\IPredisConnector;


class Get extends AbstractGet implements IPredisConnector
{
	use \Squanch\Plugins\Predis\Connector\TPredisConnector;
	
	
	private function getFullKey(CallbackData $data)
	{
		return "{$data->Bucket}:{$data->Key}";
	}
	
	
	protected function onUpdateTTL(CallbackData $data, int $newTTL)
	{
		$this->getClient()->expire($this->getFullKey($data), $newTTL);
	}
	
	/**
	 * @param CallbackData $data
	 * @return Data|null
	 */
	protected function onGet(CallbackData $data)
	{
		$item = $this->getClient()->hgetall($this->getFullKey($data));
		var_dump($item); die;
		return ($item ? Data::deserialize($item) : null);
	}
}