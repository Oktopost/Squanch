<?php
namespace Squanch\Base\Command;


use Squanch\Base\ICallback;


interface ICmdCallback
{
	/**
	 * @return static
	 */
	public function flushCallbacks();
	
	/**
	 * @param ICallback|\Closure $onSuccess
	 * @return static
	 */
	public function onSuccess($onSuccess);
	
	/**
	 * @param ICallback|\Closure $onFail
	 * @return static
	 */
	public function onFail($onFail);
	
	/**
	 * @param ICallback|\Closure $onComplete
	 * @return static
	 */
	public function onComplete($onComplete);
}