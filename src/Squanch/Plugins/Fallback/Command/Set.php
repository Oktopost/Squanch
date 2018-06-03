<?php
namespace Squanch\Plugins\Fallback\Command;


use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdSet;

use Squanch\Objects\Data;
use Squanch\Plugins\Fallback\Utils\IFallbackPluginCommand;
use Squanch\Commands\AbstractSet;


class Set extends AbstractSet implements IFallbackPluginCommand
{
	use \Squanch\Plugins\Fallback\Utils\TFallbackPluginCommand;

	
	private function setDataForPlugin(ICachePlugin $plugin, Data $data): ICmdSet
	{
		return $plugin->set($data->Id, $data->Value, $data->Bucket)->setTTL($data->TTL);
	}
	
	
	protected function onInsert(Data $data): bool
	{
		$result = false;
		
		foreach ($this->_plugins as $plugin)
		{
			try
			{
				$result = $this->setDataForPlugin($plugin, $data)->insert() || $result;
			}
			catch (\Throwable $t)
			{
				$this->onFallback($t, $data->Bucket, $data->Id);
				continue;
			}
		}
		
		return $result;
	}

	protected function onUpdate(Data $data): bool
	{
		$result = false;
		
		foreach ($this->_plugins as $plugin)
		{
			try
			{
				$result = $this->setDataForPlugin($plugin, $data)->update() || $result;
			}
			catch (\Throwable $t)
			{
				$this->onFallback($t, $data->Bucket, $data->Id);
				continue;
			}
		}
		
		return $result;
	}

	protected function onSave(Data $data): bool
	{
		$result = false;
		
		foreach ($this->_plugins as $plugin)
		{
			try
			{
				$result = $this->setDataForPlugin($plugin, $data)->save() || $result;
			}
			catch (\Throwable $t)
			{
				$this->onFallback($t, $data->Bucket, $data->Id);
				continue;
			}
		}
		
		return $result;
	}
}