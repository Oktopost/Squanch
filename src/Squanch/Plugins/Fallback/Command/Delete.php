<?php
namespace Squanch\Plugins\Fallback\Command;


use Squanch\Plugins\Fallback\Utils\IFallbackPluginCommand;
use Squanch\Commands\AbstractDelete;


class Delete extends AbstractDelete implements IFallbackPluginCommand
{
	use \Squanch\Plugins\Fallback\Utils\TFallbackPluginCommand;
	
	
	protected function onDeleteBucket(string $bucket): bool
	{
		$result = false;
		
		foreach ($this->getPlugins() as $plugin)
		{
			try
			{
				$result = $plugin->delete()->byBucket($bucket)->execute() || $result;
			}
			catch (\Throwable $t)
			{
				$this->onFallback($t, $bucket);
				continue;
			}
		}
		
		return $result;
	}

	protected function onDeleteItem(string $bucket, string $key): bool
	{
		$result = false;
		
		foreach ($this->getPlugins() as $plugin)
		{
			try
			{
				$result = $plugin->delete($key, $bucket)->execute() || $result;
			}
			catch (\Throwable $t)
			{
				$this->onFallback($t, $bucket, $key);
				continue;
			}
		}
		
		return $result;
	}
}