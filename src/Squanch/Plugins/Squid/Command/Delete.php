<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Commands\AbstractDelete;
use Squanch\Plugins\Squid\Connector\ISquidCacheConnector;


class Delete extends AbstractDelete implements ISquidCacheConnector
{
	use \Squanch\Plugins\Squid\Connector\TSquidCacheConnector;
	

	protected function onDeleteBucket(string $bucket): bool
	{
		return (bool)$this->getMysqlConnector()->delete()
			->from($this->getTableName())
			->byField('Bucket', $bucket)
			->executeDml(true);
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		return (bool)$this->getMysqlConnector()->delete()
			->from($this->getTableName())
			->byField('Bucket',	$bucket)
			->byField('Id',		$key)
			->executeDml(true);
	}
}