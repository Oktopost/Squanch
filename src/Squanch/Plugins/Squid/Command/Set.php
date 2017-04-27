<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Objects\Data;
use Squanch\Commands\AbstractSet;

use Squanch\Plugins\Squid\Connector\ISquidConnector;


class Set extends AbstractSet implements ISquidConnector
{
	use \Squanch\Plugins\Squid\Connector\TSquidConnector;
	

	protected function onInsert(Data $data): bool
	{
		return (bool)$this->getConnector()->getConnector()
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