<?php
namespace Squanch\Plugins\Fallback\Utils;


use Squanch\Base\ICachePlugin;


trait TFallbackPluginCommand
{
	/** @var ICachePlugin[] */
	private $_plugins;
	
	/** @var IFallbackPluginExceptionHandler */
	private $onFallbackHandler;


	/**
	 * @return ICachePlugin[]
	 */
	protected function getPlugins(): array
	{
		return $this->_plugins;
	}
	
	protected function onFallback(\Throwable $t, string $bucket, ?string $key = null)
	{
		if (!$this->onFallbackHandler)
			return;
		
		$this->onFallbackHandler->onFail($t, $bucket, $key);
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
	
	/**
	 * @param null|IFallbackPluginExceptionHandler $handler
	 * @return static
	 */
	public function setOnFallback(?IFallbackPluginExceptionHandler $handler = null)
	{
		if ($handler)
			$this->onFallbackHandler = $handler;
		
		return $this;
	}
}