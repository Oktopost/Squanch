<?php
namespace Squanch\Base\Callbacks\Provider;


use Squanch\Base\Callbacks\Events\IGetEvent;


interface IGetEventProvider
{
	public function setGetEvents(IGetEvent $event);
}