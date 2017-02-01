<?php
namespace Squanch\AbstractCommand;


use Squanch\Objects\Data;
use Squanch\Base\ICallback;
use Squanch\Enum\Callbacks;
use Squanch\Base\Boot\ICallbacksLoader;

use Objection\Mapper;
use Objection\LiteObject;


abstract class AbstractGet
{
	abstract protected function getCallbacksLoader(): ICallbacksLoader;
	
	abstract protected function afterExecute();
	
	abstract protected function executeIfNeed(): bool;
	
	
	/**
	 * @return Data|bool
	 */
	abstract public function asData();
	
	
	/**
	 * @param \Closure|ICallback $onSuccess
	 * @return static
	 */
	public function onSuccess($onSuccess)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::SUCCESS_ON_GET, $onSuccess);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onFail
	 * @return static
	 */
	public function onFail($onFail)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::FAIL_ON_GET, $onFail);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onComplete
	 * @return static
	 */
	public function onComplete($onComplete)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::ON_GET, $onComplete);
		return $this;
	}
	
	/**
	 * @return array|bool
	 */
	public function asArray()
	{
		if (!$this->executeIfNeed())
			return false;
		
		$data = json_decode($this->asData()->Value, true);
		$result = is_array($data) ? $data: false;
		$this->afterExecute();
		
		return $result;
	}
	
	/**
	 * @return \stdClass|bool
	 */
	public function asObject()
	{
		if (!$this->executeIfNeed())
			return false;
		
		$data = json_decode($this->asData()->Value);
		$result = is_object($data) ? $data: false;
		$this->afterExecute();
		
		return $result;
	}
	
	/**
	 * @return LiteObject|bool
	 */
	public function asLiteObject(string $liteObjectName)
	{
		if (!$this->executeIfNeed())
			return false;
		
		$result = Mapper::getObjectFrom($liteObjectName, $this->asData()->Value);

		$this->afterExecute();
		
		return $result;
	}
	
	/**
	 * @return string|bool
	 */
	public function asString()
	{
		if (!$this->executeIfNeed())
			return false;
		
		$data = json_decode($this->asData()->Value);
		$result = is_string($data) ? $data : false;
		$this->afterExecute();
		
		return $result;
	}
	
	/**
	 * @return int|float|bool
	 */
	public function asNumber()
	{
		if (!$this->executeIfNeed())
			return false;
		
		$data = json_decode($this->asData()->Value);
		$result = is_numeric($data) ? $data : false;
		$this->afterExecute();
		
		return $result;
	}
}