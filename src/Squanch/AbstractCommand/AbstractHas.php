<?php
namespace Squanch\AbstractCommand;


use Squanch\Base\ICallback;
use Squanch\Enum\Callbacks;
use Squanch\Base\Boot\ICallbacksLoader;


abstract class AbstractHas
{
	abstract protected function getCallbacksLoader(): ICallbacksLoader;
	
	
	/**
	 * @param \Closure|ICallback $onSuccess
	 * @return static
	 */
	public function onSuccess($onSuccess)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::SUCCESS_ON_HAS, $onSuccess);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onFail
	 * @return static
	 */
	public function onFail($onFail)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::FAIL_ON_HAS, $onFail);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onComplete
	 * @return static
	 */
	public function onComplete($onComplete)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::ON_HAS, $onComplete);
		return $this;
	}
}