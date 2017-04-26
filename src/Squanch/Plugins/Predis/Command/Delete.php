<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Commands\AbstractDelete;
use Squanch\Plugins\Predis\Connector\IPredisConnector;

use Predis\Client;


class Delete extends AbstractDelete implements IPredisConnector
{
	use \Squanch\Plugins\Predis\Connector\TPredisConnector;
	
	
	protected function onDeleteBucket(string $bucket): bool
	{
		throw new \Exception('Deleting a bucket is not supported!');
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		return ($this->getClient()->del(["$bucket:$key"]) > 0);
	}
}