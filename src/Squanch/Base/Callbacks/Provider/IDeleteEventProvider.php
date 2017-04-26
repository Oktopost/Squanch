<?php
namespace Squanch\Base\Callbacks\Provider;


use Squanch\Base\Callbacks\Events\IDeleteEvent;


interface IDeleteEventProvider
{
	public function setDeleteEvents(IDeleteEvent $event);
}