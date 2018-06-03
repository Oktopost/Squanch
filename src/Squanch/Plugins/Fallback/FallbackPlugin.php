<?php
namespace Squanch\Plugins\Fallback;


use Squanch\Base\Command;
use Squanch\Base\ICachePlugin;
use Squanch\Plugins\AbstractPlugin;

use Squanch\Plugins\Fallback\Command\Get;
use Squanch\Plugins\Fallback\Command\Has;
use Squanch\Plugins\Fallback\Command\Set;
use Squanch\Plugins\Fallback\Command\Delete;
use Squanch\Plugins\Fallback\Utils\IFallbackPluginExceptionHandler;


class FallbackPlugin extends AbstractPlugin implements ICachePlugin
{
	/** @var ICachePlugin[] */
	private $plugins = [];
	
	/** @var IFallbackPluginExceptionHandler */
	private $onFallback = null;
	

	protected function getCmdGet(): Command\ICmdGet
	{
		return (new Get())->setPlugins($this->plugins)->setOnFallback($this->onFallback);
	}

	protected function getCmdHas(): Command\ICmdHas
	{
		return (new Has())->setPlugins($this->plugins)->setOnFallback($this->onFallback);
	}

	protected function getCmdDelete(): Command\ICmdDelete
	{
		return (new Delete())->setPlugins($this->plugins)->setOnFallback($this->onFallback);
	}

	protected function getCmdSet(): Command\ICmdSet
	{
		return (new Set())->setPlugins($this->plugins)->setOnFallback($this->onFallback);
	}


	/**
	 * @param ICachePlugin[]|ICachePlugin $plugins
	 */
	public function __construct($plugins = [], ?IFallbackPluginExceptionHandler $onFallback = null)
	{
		parent::__construct();
		
		if ($plugins)
			$this->add($plugins);
		
		if ($onFallback)
			$this->onFallback = $onFallback;
	}
	
	/**.
	 * @param ICachePlugin[]|ICachePlugin $plugins
	 * @return static
	 */
	public function add($plugin)
	{
		if (is_array($plugin))
		{
			$this->plugins = array_merge($this->plugins, $plugin);
		}
		else
		{
			$this->plugins[] = $plugin;
		}
		
		return $this;
	}
}