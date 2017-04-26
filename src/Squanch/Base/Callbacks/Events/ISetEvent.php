<?php
namespace Squanch\Base\Callbacks\Events;


use Squanch\Objects\Data;


interface ISetEvent 
{
	public function onInsert(Data $data);
	public function onUpdate(Data $data);
	public function onSave(Data $data);
}