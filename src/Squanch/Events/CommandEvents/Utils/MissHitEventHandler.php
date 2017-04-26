<?php
namespace Squanch\Events\CommandEvents\Utils;


use Squanch\Core\EventObject;
use Squanch\Base\Callbacks\Events\Utils\IHitMissEvent;
use Squanch\Base\Callbacks\Consumer\Utils\IOnHitMiss;
use Squanch\Base\Callbacks\HandlerClasses\IIdentifierHandler;


class MissHitEventHandler implements IOnHitMiss, IHitMissEvent
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


	public function triggerHit(string $bucket, string $key)
	{
		$this->hit->invoke($bucket, $key);
	}

	public function triggerMiss(string $bucket, string $key)
	{
		$this->miss->invoke($bucket, $key);
	}

	/**
	 * @param IIdentifierHandler|callable $callback
	 */
	public function onHit($callback)
	{
		if ($callback instanceof IIdentifierHandler)
		{
			$this->hit->add(function(string $bucket, string $key) use ($callback) {
				$callback->handle($bucket, $key);
			});
		}
		else
		{
			$this->hit->add($callback);
		}
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