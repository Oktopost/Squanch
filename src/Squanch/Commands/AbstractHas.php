<?php
namespace Squanch\Commands;


use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Callbacks\Events\IHasEvent;
use Squanch\Objects\CallbackData;


abstract class AbstractHas implements ICmdHas
{
	use \Squanch\Commands\Traits\TResetTTL;
	use \Squanch\Commands\Traits\TWhere;

	
	/** @var IHasEvent */
	private $event;
	
	
	protected abstract function onCheck(CallbackData $data): bool;
	protected abstract function onUpdateTTL(CallbackData $data, int $ttl);
	
	
	public function setHasEvents(IHasEvent $event)
	{
		$this->event = $event;
	}


	public function check(): bool
	{
		if (!$this->key())
			throw new \Exception('A key must be provided for the has command');
		
		$result = $this->onCheck($this->dataObject());
		
		if ($result && $this->hasTTL())
		{
			$this->onUpdateTTL($this->dataObject(), $this->getTTL());
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