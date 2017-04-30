<?php
namespace Squanch\Plugins\InMemoryCache\Command;


use Squanch\Commands\AbstractGet;
use Squanch\Objects\CallbackData;
use Squanch\Objects\Data;
use Squanch\Plugins\InMemoryCache\Base\IStorage;


class Get extends AbstractGet
{
	/** @var IStorage */
	private $storage;
	
	
	public function __construct(IStorage $storage)
	{
		$this->storage = $storage;
	}
	
	
	/**
	 * @param CallbackData $data
	 * @return Data|null
	 */
	protected function onGet(CallbackData $data)
	{
		return $this->storage->getItemIfExists($data->Bucket, $data->Key);
	}

	protected function onUpdateTTL(CallbackData $data, int $ttl)
	{
		$data = $this->onGet($data);
		
		if ($data) 
		{
			$data->setTTL($ttl);
		}
	}
}