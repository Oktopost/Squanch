<?php
namespace Squanch\Base\Callbacks\Provider;


use Squanch\Base\Callbacks\Events\ISetEvent;


interface ISetEventProvider
{
	public function setSetEvents(ISetEvent $event);
}