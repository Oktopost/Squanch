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

	/**
	 * @param string $bucket
	 * @param string $key
	 * @return static
	 */
	public function byIdentifier(string $bucket, string $key);
}