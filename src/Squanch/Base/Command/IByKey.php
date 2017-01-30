<?php
namespace Squanch\Base\Command;


interface IByKey
{
	/**
	 * @return static
	 */
	public function byKey(string $key);
}