<?php
namespace Squanch\Base\Callbacks\Events\Utils;


interface IHitEvent
{
	public function onHit(string $bucket, string $key);
}