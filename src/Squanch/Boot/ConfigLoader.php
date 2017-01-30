<?php
namespace Squanch\Boot;


use Squanch\Base\ICallback;
use Squanch\Enum\Callbacks;
use Squanch\Objects\Instance;
use Squanch\Base\Boot\IConfigLoader;
use Squanch\Exceptions\SquanchInstanceException;


class ConfigLoader implements IConfigLoader
{
	private $instances = [];
	private $callbacks = [];
	
	
	public function addInstance(Instance $instance, bool $override = false): IConfigLoader
	{
		$instanceWorkName = $instance->Name . $instance->Type . $instance->Priority;
		
		if (isset($this->instances[$instanceWorkName]))
		{
			throw new SquanchInstanceException('Instance with the same parameters already exists. Use override.');
		}
		
		$this->instances[$instanceWorkName] = $instance;
		
		return $this;
	}
	
	/**
	 * @return Instance[]
	 */
	public function getInstances(): array
	{
		return array_values($this->instances);
	}
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setSuccessOnGetCallback($callback): IConfigLoader
	{
		$this->callbacks[Callbacks::SUCCESS_ON_GET] = [$callback];
		return $this;
	}
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setFailOnGetCallback($callback): IConfigLoader
	{
		$this->callbacks[Callbacks::FAIL_ON_GET] = [$callback];
		return $this;
	}
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setSuccessOnSetCallback($callback): IConfigLoader
	{
		$this->callbacks[Callbacks::SUCCESS_ON_SET] = [$callback];
		return $this;
	}
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setFailOnSetCallback($callback): IConfigLoader
	{
		$this->callbacks[Callbacks::FAIL_ON_SET] = [$callback];
		return $this;
	}
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setSuccessOnHasCallback($callback): IConfigLoader
	{
		$this->callbacks[Callbacks::SUCCESS_ON_HAS] = [$callback];
		return $this;
	}
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setFailOnHasCallback($callback): IConfigLoader
	{
		$this->callbacks[Callbacks::FAIL_ON_HAS] = [$callback];
		return $this;
	}
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setSuccessOnDeleteCallback($callback): IConfigLoader
	{
		$this->callbacks[Callbacks::SUCCESS_ON_DELETE] = [$callback];
		return $this;
	}
	
	/**
	 * @param ICallback|\Closure $callback
	 */
	public function setFailOnDeleteCallback($callback): IConfigLoader
	{
		$this->callbacks[Callbacks::FAIL_ON_DELETE] = [$callback];
		return $this;
	}
	
	public function getCallbacks(): array
	{
		return $this->callbacks;
	}
}