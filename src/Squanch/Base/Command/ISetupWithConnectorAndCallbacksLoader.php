<?php
namespace Squanch\Base\Command;


use Squanch\Base\Boot\ICallbacksLoader;


interface ISetupWithConnectorAndCallbacksLoader
{
	/**
	 * @return static
	 */
	public function setup($connector, ICallbacksLoader $callbacksLoader);
}