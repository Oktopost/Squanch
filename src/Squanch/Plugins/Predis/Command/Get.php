<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Commands\AbstractGet;
use Predis\Client;


/**
 * @method Client getConnector()
 */
class Get extends AbstractGet
{
	private function getFullKey(CallbackData $data)
	{
		return "{$data->Bucket}:{$data->Key}";
	}
	
	
	protected function onUpdateTTL(CallbackData $data, int $newTTL)
	{
		$this->getConnector()->expire($this->getFullKey($data), $newTTL);
	}
	
	/**
	 * @param CallbackData $data
	 * @return Data|null
	 */
	protected function onGet(CallbackData $data)
	{
		$item = $this->getConnector()->hgetall($this->getFullKey($data));
		return ($item ? Data::deserialize($item) : null);
	}
}