<?php
namespace Squanch\Base\Callbacks\Consumer;


interface IOnSet 
{
	/**
	 * @param callable $callback
	 */
	public function onInsert($callback);
	
	/**
	 * @param callable $callback
	 */
	public function onUpdate($callback);
	
	/**
	 * @param callable $callback
	 */
	public function onSave($callback);
}