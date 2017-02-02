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
	public function setSuccessOnGetCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setFailOnGetCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setOnGetCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setSuccessOnSetCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setFailOnSetCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setOnSetCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setSuccessOnHasCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setFailOnHasCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setOnHasCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setSuccessOnDeleteCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setFailOnDeleteCallback($callback): IConfigLoader;
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setOnDeleteCallback($callback): IConfigLoader;
	
	public function getCallbacks(): array;
}