<?php
namespace Squanch\Base\Boot;


use Squanch\Base\ICallback;
use Squanch\Objects\Instance;


interface IConfigLoader
{
	public function addInstance(Instance $instance, bool $override = false): IConfigLoader;
	
	public function getInstances(): array;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function callback(string $callbackType, $callback): IConfigLoader;
	
	public function getCallbacks(): array;
}