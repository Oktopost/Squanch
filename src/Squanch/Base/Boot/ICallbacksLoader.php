<?php
namespace Squanch\Base\Boot;


use Squanch\Base\ICallback;
use Squanch\Objects\CallbackData;


interface ICallbacksLoader
{
	/**
	 * @param string $callbackType
	 * @param ICallback|\Closure $callback
	 */
	public function addCallback(string $callbackType, $callback);
	
	public function executeCallback(string $callbackType, CallbackData $data);
}