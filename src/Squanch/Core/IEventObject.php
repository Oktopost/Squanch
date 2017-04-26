<?php
namespace Squanch\Core;


/**
 * @skeleton
 */
interface IEventObject
{
	public function add($callback);
	public function invoke(...$args);
	public function __clone();
}