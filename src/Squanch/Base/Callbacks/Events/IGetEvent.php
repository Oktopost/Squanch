<?php
namespace Squanch\Base\Callbacks\Events;


use Squanch\Base\Callbacks\Events\Utils\IMissEvent;
use Squanch\Objects\Data;


interface IGetEvent extends IMissEvent
{
	public function triggerHit(Data $data);
}