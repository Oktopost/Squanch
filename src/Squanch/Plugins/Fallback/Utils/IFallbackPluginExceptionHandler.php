<?php
namespace Squanch\Plugins\Fallback\Utils;


interface IFallbackPluginExceptionHandler
{
	public function onFail(\Throwable $t, string $bucket, ?string $key = null);
}