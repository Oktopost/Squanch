<?php
namespace Squanch\Base\Callbacks\Events\Utils;


interface IMissEvent
{
	public function triggerMiss(string $bucket, string $key);
}