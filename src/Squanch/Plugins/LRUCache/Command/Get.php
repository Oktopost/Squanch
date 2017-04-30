<?php
namespace Squanch\Plugins\LRUCache\Command;


use Squanch\Commands\AbstractGet;
use Squanch\Objects\CallbackData;
use Squanch\Plugins\LRUCache\Base\ILRUAdapter;


class Get extends AbstractGet
{
	/** @var ILRUAdapter */
	private $lruAdapter;
	
	
	protected function onGet(CallbackData $data)
	{
		return $this->lruAdapter->getItemIfExists($data->Bucket, $data->Key);
	}

	protected function onUpdateTTL(CallbackData $data, int $ttl)
	{
		$this->lruAdapter->setTTL($data->Bucket, $data->Key, $ttl);
	}

	
	public function __construct(ILRUAdapter $lruAdapter)
	{
		$this->lruAdapter = $lruAdapter;
	}
}