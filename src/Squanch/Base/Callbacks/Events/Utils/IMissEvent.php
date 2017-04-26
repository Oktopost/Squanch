<?php
namespace Squanch\Base\Callbacks\Events\Utils;


interface IMissEvent
{
	public function onMiss(string $bucket, string $key);
}