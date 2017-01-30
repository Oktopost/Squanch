<?php
namespace Squanch\AbstractCommand;


use Objection\LiteObject;

use Squanch\Objects\Data;
use Squanch\Base\ICallback;
use Squanch\Enum\Callbacks;
use Squanch\Base\Boot\ICallbacksLoader;


abstract class AbstractGet
{
	abstract protected function getCallbacksLoader(): ICallbacksLoader;
	
	abstract protected function afterExecute();
	
	abstract protected function executeIfNeed();
	
	
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
		$this->executeIfNeed();
		
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
		$this->executeIfNeed();
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
		$this->executeIfNeed();
		
		/** @var LiteObject $object */
		$object = new $liteObjectName;
		$object->fromArray($this->asArray());
		$this->afterExecute();
		
		return $object;
	}
	
	/**
	 * @return string|bool
	 */
	public function asString()
	{
		$this->executeIfNeed();
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
		$this->executeIfNeed();
		$data = json_decode($this->asData()->Value);
		$result = is_numeric($data) ? $data : false;
		$this->afterExecute();
		
		return $result;
	}
}