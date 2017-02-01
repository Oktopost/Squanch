<?php
namespace Squanch\Base\Command;


interface IWhere
{
	/**
	 * @return static
	 */
	public function byKey(string $key);
	
	/**
	 * @return static
	 */
	public function byBucket(string $bucket);
}