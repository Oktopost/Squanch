<?php
namespace Squanch\Base\Command;


use Squanch\Base\ICallback;


interface ICmdGetCallback
{
	/**
	 * @param ICallback|\Closure|string $callback
	 * @return static
	 */
	public function onMiss($callback);
}