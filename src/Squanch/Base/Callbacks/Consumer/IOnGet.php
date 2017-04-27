<?php
namespace Squanch\Base\Callbacks\Consumer;


use Squanch\Base\Callbacks\Consumer\Utils\IOnMiss;


interface IOnGet extends IOnMiss
{
	public function onHit($callback);
}