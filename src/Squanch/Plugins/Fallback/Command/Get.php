<?php
namespace Squanch\Plugins\Fallback\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Plugins\Fallback\Utils\IFallbackPluginCommand;
use Squanch\Commands\AbstractGet;


class Get extends AbstractGet implements IFallbackPluginCommand
{
	use \Squanch\Plugins\Fallback\Utils\TFallbackPluginCommand;


	/**
	 * @param CallbackData $data
	 * @return Data|null
	 */
	protected function onGet(CallbackData $data)
	{
		foreach ($this->getPlugins() as $plugin)
		{
			try
			{
				$obj = $plugin->get($data->Key, $data->Bucket)->asData();
			}
			catch (\Throwable $t)
			{
				$this->onFallback($t, $data->Bucket, $data->Key);
				continue;
			}
			
			if ($obj)
			{
				return $obj;
			}
		}
		
		return null;
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