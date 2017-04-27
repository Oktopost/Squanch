<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Commands\AbstractDelete;
use Squanch\Plugins\Squid\Connector\ISquidConnector;


class Delete extends AbstractDelete implements ISquidConnector
{
	use \Squanch\Plugins\Squid\Connector\TSquidConnector;
	

	protected function onDeleteBucket(string $bucket): bool
	{
		return (bool)$this->getConnector()->getConnector()->delete()
			->from($this->getTableName())
			->byField('Bucket', $bucket)
			->executeDml(true);
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		return (bool)$this->getConnector()->getConnector()->delete()
			->from($this->getTableName())
			->byField('Bucket',	$bucket)
			->byField('Id',		$key)
			->executeDml(true);
	}
}