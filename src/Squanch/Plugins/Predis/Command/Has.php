<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Objects\CallbackData;
use Squanch\Commands\AbstractHas;
use Predis\Client;


/**
 * @method Client getConnector()
 */
class Has extends AbstractHas
{
	private function getFullKey(CallbackData $data)
	{
		return "{$data->Bucket}:{$data->Key}";
	}
	
	
	protected function onUpdateTTL(CallbackData $data, int $newTTL)
	{
		$this->getConnector()->expire($this->getFullKey($data), $newTTL);
	}
	
	protected function onCheck(CallbackData $data): bool
	{
		return ($this->getConnector()->exists($this->getFullKey($data)) > 0);
	}
}