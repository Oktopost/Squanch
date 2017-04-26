<?php
namespace Squanch\Base\Command;


use Squanch\Base\Callbacks\Provider\ISetEventProvider;


interface ICmdSet extends ISetEventProvider
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
	 * @return bool
	 */
	public function insert();
	
	/**
	 * @return bool
	 */
	public function update();
	
	/**
	 * @return bool
	 */
	public function save();
}