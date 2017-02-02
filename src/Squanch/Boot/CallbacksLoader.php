<?php
namespace Squanch\Boot;


use Squanch\Base\ICallback;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Objects\CallbackData;


class CallbacksLoader implements ICallbacksLoader
{
	private $callbacks = [];
	
	
	/**
	 * @param ICallback[]|\Closure[] $callbacks
	 */
	public function addCallback(string $callbackType, $callback)
	{
		if (!isset($this->callbacks[$callbackType]))
		{
			$this->callbacks[$callbackType] = [];
		}
		
		$this->callbacks[$callbackType][] = $callback;
	}
	
	public function executeCallback(string $callbackType, CallbackData $data)
	{
		if (isset($this->callbacks[$callbackType]))
		{
			foreach ($this->callbacks[$callbackType] as $callback)
			{
				if (is_string($callback))
				{
					$callback = new $callback();
				}
				
				if ($callback instanceof ICallback)
				{
					/** @var ICallback $impl */
					$impl = new $callback();
					$impl->fire($data);
				}
				else if ($callback instanceof \Closure)
				{
					$callback($data);
				}
			}
		}
	}
}