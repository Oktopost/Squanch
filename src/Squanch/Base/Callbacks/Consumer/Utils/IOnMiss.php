<?php
namespace Squanch\Base\Callbacks\Consumer\Utils;


use Squanch\Base\Callbacks\HandlerClasses\IIdentifierHandler;

interface IOnMiss
{
	/**
	 * @param IIdentifierHandler|callable $callback
	 */
	public function onMiss($callback);
}