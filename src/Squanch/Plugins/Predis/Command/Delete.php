<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Commands\AbstractDelete;
use Squanch\Exceptions\OperationNotSupportedOnBucketException;
use Squanch\Plugins\Predis\Connector\IPredisConnector;

use Predis\Client;


class Delete extends AbstractDelete implements IPredisConnector
{
	use \Squanch\Plugins\Predis\Connector\TPredisConnector;
	
	
	protected function onDeleteBucket(string $bucket): bool
	{
		throw new OperationNotSupportedOnBucketException('redis::delete');
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		return ($this->getClient()->del(["$bucket:$key"]) > 0);
	}
}