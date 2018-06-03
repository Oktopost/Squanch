<?php
namespace Squanch\Plugins\Fallback\Command;


use Squanch\Objects\CallbackData;
use Squanch\Plugins\Fallback\Utils\IFallbackPluginCommand;
use Squanch\Commands\AbstractHas;


class Has extends AbstractHas implements IFallbackPluginCommand
{
	use \Squanch\Plugins\Fallback\Utils\TFallbackPluginCommand;


	protected function onCheck(CallbackData $data): bool
	{
		foreach ($this->_plugins as $plugin)
		{
			try
			{
				if ($plugin->has($data->Key, $data->Bucket)->check())
				{
					return true;
				}
			}
			catch (\Throwable $t)
			{
				$this->onFallback($t, $data->Bucket, $data->Key);
				continue;
			}
		}
		
		return false;
	}

	protected function onUpdateTTL(CallbackData $data, int $ttl)
	{
		foreach ($this->_plugins as $plugin)
		{
			try
			{
				$plugin->has($data->Key, $data->Bucket)->resetTTL($ttl)->check();
			}
			catch (\Throwable $t)
			{
				$this->onFallback($t, $data->Bucket, $data->Key);
				continue;
			}
		}
	}
}