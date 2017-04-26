<?php
namespace Squanch\Commands;


use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Callbacks\Events\IDeleteEvent;


abstract class AbstractDelete implements ICmdDelete
{
	use \Squanch\Commands\Traits\TWhere;
	
	
	/** @var IDeleteEvent */
	private $event;
	
	
	protected abstract function onDeleteBucket(string $bucket): bool;
	protected abstract function onDeleteItem(string $bucket, string $key): bool;
	
	
	public function setDeleteEvents(IDeleteEvent $event)
	{
		$this->event = $event;
	}

	public function execute(): bool
	{
		if ($this->key())
		{
			$result = $this->onDeleteItem($this->bucket(), $this->key());
		}
		else
		{
			$result = $this->onDeleteBucket($this->bucket());
		}
		
		if ($result)
		{
			$this->event->triggerHit($this->bucket(), $this->key());
		}
		else
		{
			$this->event->triggerMiss($this->bucket(), $this->key());
		}
		
		return $result;
	}
}