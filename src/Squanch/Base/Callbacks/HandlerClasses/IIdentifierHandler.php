<?php
namespace Squanch\Base\Callbacks\HandlerClasses;


interface IIdentifierHandler
{
	public function handle(string $bucket, string $key);
}