<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Commands\AbstractDelete;
use Predis\Client;


/**
 * @method Client getConnector()
 */
class Delete extends AbstractDelete
{
	protected function onDeleteBucket(string $bucket): bool
	{
		throw new \Exception('Deleting a bucket is not supported!');
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		return ($this->getConnector()->del(["$bucket:$key"]) > 0);
	}
}