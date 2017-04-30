<?php
namespace Squanch\Plugins\Fallback\Utils;


use Squanch\Base\ICachePlugin;


trait TFallbackPluginCommand
{
	/** @var ICachePlugin[] */
	private $_plugins;


	/**
	 * @return ICachePlugin[]
	 */
	protected function getPlugins(): array
	{
		return $this->_plugins;
	}
	
	
	/**
	 * @param ICachePlugin[] $plugins
	 * @return static
	 */
	public function setPlugins(array $plugins): IFallbackPluginCommand
	{
		$this->_plugins = $plugins;
		return $this;
	}
}