<?php
namespace Squanch\Base\Command;


use Squanch\Base\ICallback;


interface ICmdCallback
{
	/**
	 * @param ICallback|\Closure|string $onSuccess
	 * @return static
	 */
	public function onSuccess($onSuccess);
	
	/**
	 * @param ICallback|\Closure|string $onFail
	 * @return static
	 */
	public function onFail($onFail);
	
	/**
	 * @param ICallback|\Closure|string $onComplete
	 * @return static
	 */
	public function onComplete($onComplete);
}