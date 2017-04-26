<?php
namespace Squanch\Base\Callbacks\Consumer\Utils;


use Squanch\Base\Callbacks\HandlerClasses\IIdentifierHandler;


interface IOnHit
{
	/**
	 * @param IIdentifierHandler|callable $callback
	 */
	public function onHit($callback);
}