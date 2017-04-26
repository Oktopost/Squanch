<?php
namespace Squanch\Base\Callbacks;


use Squanch\Base\Callbacks\Events;


interface ICacheEventsProvider
{
	public function hasEvent(): Events\IHasEvent;
	public function getEvent(): Events\IGetEvent;
	public function setEvent(): Events\ISetEvent;
	public function deleteEvent(): Events\IDeleteEvent;
}