<?php
namespace Squanch\Plugins\Fallback\Command;


use Squanch\Objects\Data;
use Squanch\Plugins\Fallback\Utils\IFallbackPluginCommand;
use Squanch\Commands\AbstractSet;


class Set extends AbstractSet implements IFallbackPluginCommand
{
	use \Squanch\Plugins\Fallback\Utils\TFallbackPluginCommand;

	
	protected function onInsert(Data $data): bool
	{
		$result = false;
		
		foreach ($this->_plugins as $plugin)
		{
			$result = $plugin->set($data->Id, $data->Value, $data->Bucket)->insert() || $result;
		}
		
		return $result;
	}

	protected function onUpdate(Data $data): bool
	{
		$result = false;
		
		foreach ($this->_plugins as $plugin)
		{
			$result = $plugin->set($data->Id, $data->Value, $data->Bucket)->update() || $result;
		}
		
		return $result;
	}

	protected function onSave(Data $data): bool
	{
		$result = false;
		
		foreach ($this->_plugins as $plugin)
		{
			$result = $plugin->set($data->Id, $data->Value, $data->Bucket)->save() || $result;
		}
		
		return $result;
	}
}