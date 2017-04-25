<?php
namespace Squanch\Base\Command;


interface ICmdSet extends ISetupWithConnectorAndCallbacksLoader
{
	/**
	 * @return static
	 */
	public function setBucket(string $bucket);
	
	/**
	 * @return static
	 */
	public function setKey(string $key);
	
	/**
	 * @return static
	 */
	public function setData($data);
	
	/**
	 * @return static
	 */
	public function setTTL(int $ttl);
	
	/**
	 * @return static
	 */
	public function setForever();
	
	/**
	 * @return static
	 */
	public function insert();
	
	/**
	 * @return static
	 */
	public function update();
	
	/**
	 * @return static
	 */
	public function save();
}