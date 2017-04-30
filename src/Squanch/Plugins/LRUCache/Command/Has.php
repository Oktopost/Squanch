<?php
namespace Squanch\Plugins\LRUCache\Command;


use Squanch\Commands\AbstractHas;
use Squanch\Objects\CallbackData;
use Squanch\Plugins\LRUCache\Base\ILRUAdapter;


class Has extends AbstractHas
{
	/** @var ILRUAdapter */
	private $lruAdapter;
	
	
	protected function onCheck(CallbackData $data): bool
	{
		return $this->lruAdapter->hasKey($data->Bucket, $data->Key);
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