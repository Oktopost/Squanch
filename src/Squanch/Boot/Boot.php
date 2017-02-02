<?php
namespace Squanch\Boot;


use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Base\ICachePlugin;
use Squanch\Base\Boot\IBoot;
use Squanch\Base\Boot\IConfigLoader;

use Squanch\Objects\Instance;
use Squanch\Exceptions\SquanchInstanceException;


/**
 * @autoload
 */
class Boot implements IBoot
{
	/**
	 * @autoload
	 * @var \Squanch\Base\Boot\ICallbacksLoader $callbacksLoader
	 */
	private $callbacksLoader;
	
	/** @var IConfigLoader $config */
	private $config;
	
	/** @var Instance[] $filteredInstances */
	private $filteredInstances;
	
	
	private function filterInstances(\Closure $compare)
	{
		if (is_null($this->filteredInstances))
		{
			$this->resetFilters();
		}
		
		$instances = [];
		
		foreach ($this->filteredInstances as $instance)
		{
			if ($compare($instance))
			{
				$instances[] = $instance;
			}
		}
		
		$this->filteredInstances = $instances;
	}
	
	private function getCallbacksLoaderWithCallbacks(): ICallbacksLoader
	{
		/** @var ICallbacksLoader $loader */
		$loader = $this->callbacksLoader;
		
		foreach ($this->config->getCallbacks() as $key => $value)
		{
			$loader->addCallback($key, $value, true);
		}
		
		return $this->callbacksLoader;
	}
	
	
	public function resetFilters()
	{
		$this->filteredInstances = $this->config->getInstances();
		$callbacksLoader = $this->getCallbacksLoaderWithCallbacks();
		
		foreach ($this->filteredInstances as $instance)
		{
			$instance->Plugin->setCallbacksLoader($callbacksLoader);
		}
		
		return $this;
	}
	
	public function setConfigLoader(IConfigLoader $configLoader): IBoot
	{
		$this->config = $configLoader;
		return $this;
	}
	
	public function filterInstancesByType(string $type): IBoot
	{
		$this->filterInstances(
			function($instance) use ($type)
			{
				return $instance->Type == $type ? true : false;
			}
		);
		
		return $this;
	}
	
	public function filterInstancesByName(string $name): IBoot
	{
		$this->filterInstances(
			function($instance) use ($name)
			{
				return $instance->Name == $name ? true : false;
			}
		);
		
		return $this;
	}
	
	public function filterInstancesByPriorityLessOrEqual(int $priority): IBoot
	{
		$this->filterInstances(
			function($instance) use ($priority)
			{
				return $instance->Priority <= $priority ? true : false;
			}
		);
		
		return $this;
	}
	
	public function filterInstancesByPriorityGreaterOrEqual(int $priority): IBoot
	{
		$this->filterInstances(
			function($instance) use ($priority)
			{
				return $instance->Priority >= $priority ? true : false;
			}
		);
		
		return $this;
	}
	
	public function filterInstancesByPriority(int $priority): IBoot
	{
		$this->filterInstances(
			function($instance) use ($priority)
			{
				return $instance->Priority == $priority ? true : false;
			}
		);
		
		return $this;
	}
	
	public function getPlugin(): ICachePlugin
	{
		$total = count($this->filteredInstances);
		
		if ($total == 0)
		{
			throw new SquanchInstanceException('Required instance not found');
		}
		else if ($total > 1)
		{
			throw new SquanchInstanceException('Got multiple instances with same properties');
		}
		
		/** @var Instance $instance */
		$instance = array_values($this->filteredInstances)[0];
		
		return $instance->Plugin;
	}
}