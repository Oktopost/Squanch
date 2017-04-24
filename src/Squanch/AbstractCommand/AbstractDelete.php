<?php
namespace Squanch\AbstractCommand;


use Squanch\Base\ICallback;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;

use Squanch\Enum\Bucket;
use Squanch\Enum\Callbacks;


abstract class AbstractDelete implements ICmdDelete
{
	private $connector;
	private $key;
	private $bucket = Bucket::DEFAULT_BUCKET_NAME;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	
	
	protected function getConnector()
	{
		return $this->connector;
	}
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
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
		$this->callbacksLoader->addCallback(Callbacks::SUCCESS_ON_DELETE, $onSuccess);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onFail
	 * @return static
	 */
	public function onFail($onFail)
	{
		$this->callbacksLoader->addCallback(Callbacks::FAIL_ON_DELETE, $onFail);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onComplete
	 * @return static
	 */
	public function onComplete($onComplete)
	{
		$this->callbacksLoader->addCallback(Callbacks::ON_DELETE, $onComplete);
		return $this;
	}
}