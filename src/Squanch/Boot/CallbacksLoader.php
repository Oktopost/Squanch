<?php
namespace Squanch\Boot;


use Squanch\Base\ICallback;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Objects\CallbackData;


class CallbacksLoader implements ICallbacksLoader
{
	private $globalCallbacks = [];
	private $callbacks = [];
	
	
	private function execute(array $callbacks, CallbackData $data): bool
	{
		foreach ($callbacks as $callback)
		{
			$result = true;
			
			if (is_string($callback))
			{
				$callback = new $callback();
			}
			
			if ($callback instanceof ICallback)
			{
				/** @var ICallback $impl */
				$impl = new $callback();
				$result = $impl->fire($data);
			}
			else if ($callback instanceof \Closure)
			{
				$result = call_user_func($callback, $data);
			}
			
			if (!is_null($result) && $result == false)
			{
				return false;
			}
		}
		
		return true;
	}
	
	
	public function addGlobalCallback(string $callbackType, $callback)
	{
		if (!isset($this->globalCallbacks[$callbackType]))
		{
			$this->globalCallbacks[$callbackType] = [];
		}
		
		$this->globalCallbacks[$callbackType][] = $callback;
	}
	
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
			$execute = $this->execute($this->callbacks[$callbackType], $data);
			$this->callbacks[$callbackType] = [];
			
			if (!$execute)
			{
				return;
			}
		}

		if (isset($this->globalCallbacks[$callbackType]))
		{
			$this->execute($this->globalCallbacks[$callbackType], $data);
		}
	}
}