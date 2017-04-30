<?php
namespace Squanch\Plugins\Fallback\Utils;


use Squanch\Base\ICachePlugin;


interface IFallbackPluginCommand
{
	/**
	 * @param ICachePlugin[] $plugins
	 * @return static
	 */
	public function setPlugins(array $plugins): IFallbackPluginCommand;
}