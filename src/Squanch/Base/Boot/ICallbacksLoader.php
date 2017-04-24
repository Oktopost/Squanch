<?php
namespace Squanch\Base\Boot;


use Squanch\Base\ICallback;
use Squanch\Objects\CallbackData;


interface ICallbacksLoader
{
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function addCallback(string $callbackType, $callback);
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function addGlobalCallback(string $callbackType, $callback);
	
	public function executeCallback(string $callbackType, CallbackData $data);
}