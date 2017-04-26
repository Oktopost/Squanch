<?php
namespace Squanch\Base\Callbacks\Events;


use Squanch\Objects\Data;


interface ISetEvent 
{
	public function triggerInsert(Data $data);
	public function triggerUpdate(Data $data);
	public function triggerSave(Data $data);
}