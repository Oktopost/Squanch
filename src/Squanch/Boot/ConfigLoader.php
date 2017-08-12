<?php
namespace Squanch\Boot;


use Squanch\Objects\Instance;
use Squanch\Base\Boot\IConfigLoader;
use Squanch\Exceptions\SquanchInstanceException;


class ConfigLoader implements IConfigLoader
{
	private $instances = [];
	
	
	public function addInstance(Instance $instance, bool $override = false): IConfigLoader
	{
		$instanceWorkName = $instance->Name . $instance->Type . $instance->Priority;
		
		if (isset($this->instances[$instanceWorkName]) && !$override)
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
}