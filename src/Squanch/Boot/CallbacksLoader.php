<?php
namespace Squanch\Boot;


use Squanch\Base\ICallback;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Objects\CallbackData;


class CallbacksLoader implements ICallbacksLoader
{
	private $globalCallbacks = [];
	private $callbacks = [];
	
	
	private function addGlobalCallback(string $callbackType, $callback)
	{
		if (!isset($this->globalCallbacks[$callbackType]))
		{
			$this->globalCallbacks[$callbackType] = [];
		}
		
		$this->globalCallbacks[$callbackType][] = $callback;
	}
	
	private function addRunTimeCallback(string $callbackType, $callback)
	{
		if (!isset($this->callbacks[$callbackType]))
		{
			$this->callbacks[$callbackType] = [];
		}
		
		$this->callbacks[$callbackType][] = $callback;
	}
	
	private function execute(array $callbacks, CallbackData $data)
	{
		foreach ($callbacks as $callback)
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
				call_user_func($callback, $data);
			}
		}
	}
	
	
	/**
	 * @param ICallback[]|\Closure[] $callbacks
	 */
	public function addCallback(string $callbackType, $callback, $isGlobal = false)
	{
		if ($isGlobal)
		{
			$this->addGlobalCallback($callbackType, $callback);
		}
		else
		{
			$this->addRunTimeCallback($callbackType, $callback);
		}
	}
	
	public function executeCallback(string $callbackType, CallbackData $data)
	{
		if (isset($this->globalCallbacks[$callbackType]))
		{
			$this->execute($this->globalCallbacks[$callbackType], $data);
		}
		
		if (isset($this->callbacks[$callbackType]))
		{
			$this->execute($this->callbacks[$callbackType], $data);
			$this->callbacks[$callbackType] = [];
		}
	}
	
	public function flushCallbacks()
	{
		$this->callbacks = [];
	}
}