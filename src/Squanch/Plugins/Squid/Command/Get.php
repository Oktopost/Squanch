<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Commands\AbstractGet;

use Squanch\Plugins\Squid\Connector\ISquidCacheConnector;
use Squanch\Plugins\Squid\Utils\UpdateTTL;


class Get extends AbstractGet implements ISquidCacheConnector
{
	use \Squanch\Plugins\Squid\Connector\TSquidCacheConnector;
	
	
	/**
	 * @param CallbackData $data
	 * @return Data|null
	 */
	protected function onGet(CallbackData $data)
	{
		$fields = [];
		
		if ($data->Key) $fields['Id'] = $data->Key;
		if ($data->Bucket) $fields['Bucket'] = $data->Bucket;
		
		if (!$data)
			throw new \Exception('Key/Bucket or Bucket must be provided!');
		
		$data = $this->getConnector()->loadOneByFields($fields);
		
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return ($data ?: null); 
	}
	
	protected function onUpdateTTL(CallbackData $data, int $ttl)
	{
		$updater = new UpdateTTL($this->getConnector());
		$updater->updateTTL($data, $ttl);
	}
}