<?php
namespace Squanch\AbstractCommand;


use Squanch\Base\ICallback;
use Squanch\Enum\Callbacks;
use Squanch\Base\Boot\ICallbacksLoader;


abstract class AbstractSet
{
	private $insertOnly;
	private $updateOnly;
	
	
	abstract protected function getCallbacksLoader(): ICallbacksLoader;
	
	
	protected function resetInsertAndUpdateOnly()
	{
		$this->insertOnly = false;
		$this->updateOnly = false;
	}
	
	
	/**
	 * @param \Closure|ICallback $onSuccess
	 * @return static
	 */
	public function onSuccess($onSuccess)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::SUCCESS_ON_SET, $onSuccess);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onFail
	 * @return static
	 */
	public function onFail($onFail)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::FAIL_ON_SET, $onFail);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onComplete
	 * @return static
	 */
	public function onComplete($onComplete)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::ON_SET, $onComplete);
		return $this;
	}
	
	public function insertOnly()
	{
		$this->insertOnly = true;
		$this->updateOnly = false;
		
		return $this;
	}
	
	public function updateOnly()
	{
		$this->insertOnly = false;
		$this->updateOnly = true;
		
		return $this;
	}
	
	public function isInsertOnly()
	{
		return $this->insertOnly == true;
	}
	
	public function isUpdateOnly()
	{
		return $this->updateOnly == true;
	}
}