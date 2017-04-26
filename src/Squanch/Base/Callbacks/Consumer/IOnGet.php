<?php
namespace Squanch\Base\Callbacks\Consumer;


use Squanch\Base\Callbacks\Consumer\Utils\IOnMiss;
use Squanch\Objects\Data;


interface IOnGet extends IOnMiss
{
	public function onHit($callback);
}