<?php
namespace Squanch\Events\CommandEvents;


use Squanch\Base\Callbacks\Consumer\IOnHas;
use Squanch\Base\Callbacks\Events\IHasEvent;
use Squanch\Events\CommandEvents\Utils\MissHitEventHandler;


class HasEventHandler 
	extends MissHitEventHandler 
	implements IOnHas, IHasEvent
{
	
}