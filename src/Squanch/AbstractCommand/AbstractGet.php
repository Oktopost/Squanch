<?php
namespace Squanch\AbstractCommand;


use Squanch\Objects\Data;
use Squanch\Enum\Bucket;
use Squanch\Enum\Callbacks;
use Squanch\Base\ICallback;
use Squanch\Base\Boot\ICallbacksLoader;

use Objection\Mapper;
use Objection\LiteObject;


abstract class AbstractGet
{
	private $connector;
	private $callbacksLoader;
	private $key;
	private $bucket = Bucket::DEFAULT_BUCKET_NAME;
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	protected function getConnector()
	{
		return $this->connector;
	}
	
	protected abstract function afterExecute();
	
	protected abstract function executeIfNeed(): bool;
	
	
	/**
	 * @return Data|bool
	 */
	public abstract function asData();

	
	
	protected function reset()
	{
		unset($this->key);
		$this->bucket = Bucket::DEFAULT_BUCKET_NAME;
	}
	
	
	protected function getBucket(): string
	{
		return $this->bucket;
	}
	
	protected function getKey()
	{
		return $this->key;
	}
	
	
	/**
	 * @return static
	 */
	public function setup($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
		return $this;
	}
	
	/**
	 * @return static
	 */
	public function byBucket(string $bucket)
	{
		$this->bucket = $bucket;
		return $this;
	}
	
	/**
	 * @return static
	 */
	public function byKey(string $key)
	{
		$this->key = $key;
		return $this;
	}
	
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
		
		
		$mapper = Mapper::createFor($liteObjectName);
		
		$result = $mapper->getObject($this->asArray(), $liteObjectName);
		
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
		
		$result = $this->asData()->Value;
		
		$this->afterExecute();
		
		return $result;
	}
}