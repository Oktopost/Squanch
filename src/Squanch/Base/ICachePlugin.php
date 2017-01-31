<?php
namespace Squanch\Base;


use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;


interface ICachePlugin
{
	public function setCallbacksLoader(ICallbacksLoader $callbacksLoader): ICachePlugin;
	
	public function delete(string $key = null): ICmdDelete;
	
	public function get(string $key = null): ICmdGet;
	
	public function has(string $key = null): ICmdHas;
	
	public function set(string $key = null, $data = null): ICmdSet;
}