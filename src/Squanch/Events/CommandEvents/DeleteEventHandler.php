<?php
namespace Squanch\Events\CommandEvents;


use Squanch\Base\Callbacks\Consumer\IOnDelete;
use Squanch\Base\Callbacks\Events\IDeleteEvent;
use Squanch\Events\CommandEvents\Utils\MissHitEventHandler;


class DeleteEventHandler 
	extends MissHitEventHandler 
	implements IOnDelete, IDeleteEvent
{
	
}