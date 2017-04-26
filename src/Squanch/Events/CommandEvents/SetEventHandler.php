<?php
namespace Squanch\Events\CommandEvents;


use Squanch\Core\EventObject;
use Squanch\Base\Callbacks\Events\ISetEvent;
use Squanch\Base\Callbacks\Consumer\IOnSet;
use Squanch\Objects\Data;


class SetEventHandler implements IOnSet, ISetEvent
{
	/** @var EventObject */
	private $save;
	
	/** @var EventObject */
	private $insert;
	
	/** @var EventObject */
	private $update;
	
	
	public function __construct()
	{
		$this->save = new EventObject();
		$this->insert = new EventObject();
		$this->update = new EventObject();
	}
	
	public function __clone()
	{
		$this->save = clone $this->save;
		$this->insert = clone $this->insert;
		$this->update = clone $this->update;
	}
	
	
	public function triggerInsert(Data $data)
	{
		$this->insert->invoke($data);
	}
	
	public function triggerUpdate(Data $data)
	{
		$this->update->invoke($data);
	}
	
	public function triggerSave(Data $data)
	{
		$this->save->invoke($data);
	}
	
	/**
	 * @param callable $callback
	 */
	public function onInsert($callback)
	{
		$this->insert->add($callback);
	}
	
	/**
	 * @param callable $callback
	 */
	public function onUpdate($callback)
	{
		$this->update->add($callback);
	}
	
	/**
	 * @param callable $callback
	 */
	public function onSave($callback)
	{
		$this->save->add($callback);
	}
}