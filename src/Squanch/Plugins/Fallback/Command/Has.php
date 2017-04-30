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
			if ($plugin->has($data->Key, $data->Bucket)->check())
			{
				return true;
			}
		}
		
		return false;
	}

	protected function onUpdateTTL(CallbackData $data, int $ttl)
	{
		foreach ($this->_plugins as $plugin)
		{
			$plugin->has($data->Key, $data->Bucket)->resetTTL($ttl)->check();
		}
	}
}