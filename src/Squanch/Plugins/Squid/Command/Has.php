<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Objects\CallbackData;
use Squanch\Commands\AbstractHas;

use Squanch\Plugins\Squid\Utils\UpdateTTL;
use Squanch\Plugins\Squid\Connector\ISquidConnector;


class Has extends AbstractHas implements ISquidConnector
{
	use \Squanch\Plugins\Squid\Connector\TSquidConnector;
	

	protected function onCheck(CallbackData $data): bool
	{
		return $this->getConnector()->getConnector()
			->select()
			->from($this->getTableName())
			->byField('Bucket', $data->Bucket)
			->byField('Id', $data->Key)
			->queryExists();
	}

	protected function onUpdateTTL(CallbackData $data, int $ttl)
	{
		$updater = new UpdateTTL($this->getConnector());
		$updater->updateTTL($data, $ttl);
	}
}