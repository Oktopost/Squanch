<?php
namespace Squanch\AbstractCommand;


use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Base\ICallback;
use Squanch\Enum\Callbacks;


abstract class AbstractDelete
{
	abstract protected function getCallbacksLoader(): ICallbacksLoader;
	
	
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