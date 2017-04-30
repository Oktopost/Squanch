<?php
namespace Squanch\Plugins\InMemoryCache\Command;


use Squanch\Commands\AbstractHas;
use Squanch\Objects\CallbackData;
use Squanch\Plugins\InMemoryCache\Base\IStorage;


class Has extends AbstractHas
{
	/** @var IStorage */
	private $storage;
	
	
	public function __construct(IStorage $storage)
	{
		$this->storage = $storage;
	}
	
	
	protected function onCheck(CallbackData $data): bool
	{
		return $this->storage->hasKey($data->Bucket, $data->Key);
	}

	protected function onUpdateTTL(CallbackData $data, int $ttl)
	{
		$data = $this->storage->getItemIfExists($data->Bucket, $data->Key);
		
		if ($data) 
		{
			$data->setTTL($ttl);
		}
	}
}