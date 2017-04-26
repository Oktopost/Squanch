<?php
namespace Squanch\Base\Callbacks\Provider;


use Squanch\Base\Callbacks\Events\IHasEvent;


interface IHasEventProvider
{
	public function setHasEvents(IHasEvent $event);
}