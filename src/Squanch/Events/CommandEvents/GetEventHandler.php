<?php
namespace Squanch\Events\CommandEvents;


use Squanch\Core\EventObject;
use Squanch\Base\Callbacks\Consumer\IOnGet;
use Squanch\Base\Callbacks\Events\IGetEvent;
use Squanch\Base\Callbacks\HandlerClasses\IIdentifierHandler;
use Squanch\Objects\Data;


class GetEventHandler implements IOnGet, IGetEvent
{
	/** @var EventObject */
	private $hit;
	
	/** @var EventObject */
	private $miss;
	
	
	public function __construct()
	{
		$this->hit = new EventObject();
		$this->miss = new EventObject();
	}
	
	public function __clone()
	{
		$this->hit = clone $this->hit;
		$this->miss = clone $this->miss;
	}
	
	
	public function triggerHit(Data $data)
	{
		$this->hit->invoke($data);
	}
	
	public function triggerMiss(string $bucket, string $key)
	{
		$this->miss->invoke($bucket, $key);
	}
	
	/**
	 * @param callable $callback
	 */
	public function onHit($callback)
	{
		$this->hit->add($callback);
	}
	
	/**
	 * @param IIdentifierHandler|callable $callback
	 */
	public function onMiss($callback)
	{
		if ($callback instanceof IIdentifierHandler)
		{
			$this->miss->add(function(string $bucket, string $key) use ($callback) {
				$callback->handle($bucket, $key);
			});
		}
		else
		{
			$this->miss->add($callback);
		}
	}
}