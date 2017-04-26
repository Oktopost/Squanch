<?php
namespace Squanch\Core;


class EventObject implements IEventObject
{
	/**
	 * @var array
	 */
	private $callbacks = [];

	
	public function add($callback)
	{
		$this->callbacks[] = $callback;
	}

	public function invoke(...$args)
	{
		foreach ($this->callbacks as $callback)
		{
			$callback(...$args);
		}
	}

	public function __clone() { }
}