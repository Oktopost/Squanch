<?php
namespace Squanch\Base\Callbacks;


use Squanch\Base\Callbacks\Consumer;


interface ICacheEventsConsumer
{
	public function onHas(): Consumer\IOnHas;
	public function onGet(): Consumer\IOnGet;
	public function onSet(): Consumer\IOnSet;
	public function onDelete(): Consumer\IOnDelete;
}