<?php
namespace Squanch\Plugins\Predis\Command;


use Squanch\Commands\AbstractDelete;
use Squanch\Plugins\Predis\Connector\IPredisConnector;

use Predis\Collection\Iterator\Keyspace;


class Delete extends AbstractDelete implements IPredisConnector
{
	const BUCKET_SIZE_TO_DELETE = 1000;
	
	
	use \Squanch\Plugins\Predis\Connector\TPredisConnector;
	
	
	private function delete(array $keys): bool
	{
		return $this->getClient()->del($keys) > 0;
	}
	
	
	protected function onDeleteBucket(string $bucket): bool
	{
		$iterator = new Keyspace($this->getClient(), "$bucket:*");
		$result = false;
		$keys = [];
		$currentCount = 0;
		
		foreach ($iterator as $key)
		{
			$currentCount++;
			$keys[] = $key;
			$result = true;
			
			if ($currentCount == self::BUCKET_SIZE_TO_DELETE)
			{
				$this->delete($keys);
				$keys = [];
				$currentCount = 0;
			}
		}
		
		if ($currentCount > 0)
		{
			$this->delete($keys);
		}
		
		return $result;
	}
	
	protected function onDeleteItem(string $bucket, string $key): bool
	{
		return $this->delete(["$bucket:$key"]);
	}
}