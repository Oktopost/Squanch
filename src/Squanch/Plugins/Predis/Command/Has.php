<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Objects\CallbackData;
use Squanch\Commands\AbstractHas;
use Squanch\Plugins\Predis\Utils\UpdateTTL;
use Squanch\Plugins\Predis\Connector\IPredisConnector;


class Has extends AbstractHas implements IPredisConnector
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
	
	protected function onCheck(CallbackData $data): bool
	{
		return ($this->getClient()->exists($this->getFullKey($data)) > 0);
	}
}