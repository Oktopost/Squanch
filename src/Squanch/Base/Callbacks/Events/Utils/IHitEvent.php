<?php
namespace Squanch\Base\Callbacks\Events\Utils;


interface IHitEvent
{
	public function triggerHit(string $bucket, string $key);
}