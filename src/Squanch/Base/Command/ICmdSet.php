<?php
namespace Squanch\Base\Command;


interface ICmdSet extends IConstructWithConnectorAndCallbacksLoader, ICommand
{
	public function setKey(string $key): ICmdSet;
	
	public function setData($data): ICmdSet;
	
	public function setTTL(int $ttl): ICmdSet;
	
	public function setForever(): ICmdSet;
	
	/**
	 * @return static
	 */
	public function insertOnly();
	
	/**
	 * @return static
	 */
	public function updateOnly();
}