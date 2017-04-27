<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Objects\Data;
use Squanch\Commands\AbstractSet;

use Squanch\Plugins\Squid\Connector\ISquidCacheConnector;


class Set extends AbstractSet implements ISquidCacheConnector
{
	use \Squanch\Plugins\Squid\Connector\TSquidCacheConnector;
	

	protected function onInsert(Data $data): bool
	{
		return (bool)$this->getMysqlConnector()
			->insert()
			->ignore()
			->into('HardCache')
			->values($data->toArray())
			->executeDml(true);
	}

	protected function onUpdate(Data $data): bool
	{
		return $this->getConnector()->updateObjectByFields($data, ['Id', 'Bucket']);
	}

	protected function onSave(Data $data): bool
	{
		return $this->getConnector()->upsertByFields($data, ['Id', 'Bucket']);
	}
}