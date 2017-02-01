<?php
namespace Squanch\AbstractCommand;


use Squanch\Base\ICallback;
use Squanch\Base\Boot\ICallbacksLoader;

use Squanch\Enum\Bucket;
use Squanch\Enum\Callbacks;


abstract class AbstractDelete
{
	private $key;
	private $bucket = Bucket::DEFAULT_BUCKET_NAME;
	
	
	abstract protected function getCallbacksLoader(): ICallbacksLoader;
	
	
	protected function reset()
	{
		unset($this->key);
		$this->bucket = Bucket::DEFAULT_BUCKET_NAME;
	}
	
	
	public function getBucket(): string
	{
		return $this->bucket;
	}
	
	/**
	 * @return static
	 */
	public function byBucket(string $bucket)
	{
		$this->bucket = $bucket;
		return $this;
	}
	
	public function getKey(): string
	{
		return $this->key;
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
		$this->getCallbacksLoader()->addCallback(Callbacks::SUCCESS_ON_DELETE, $onSuccess);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onFail
	 * @return static
	 */
	public function onFail($onFail)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::FAIL_ON_DELETE, $onFail);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onComplete
	 * @return static
	 */
	public function onComplete($onComplete)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::ON_DELETE, $onComplete);
		return $this;
	}
}