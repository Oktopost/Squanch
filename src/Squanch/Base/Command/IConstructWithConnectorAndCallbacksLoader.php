<?php
namespace Squanch\Base\Command;


use Squanch\Base\Boot\ICallbacksLoader;


interface IConstructWithConnectorAndCallbacksLoader
{
	public function __construct($connector, ICallbacksLoader $callbacksLoader);	
}