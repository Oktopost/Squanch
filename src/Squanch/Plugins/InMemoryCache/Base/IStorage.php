<?php
namespace Squanch\Plugins\InMemoryCache\Base;


use Squanch\Objects\Data;


interface IStorage
{
	public function storage(): array;
	
	public function hasBucket(string $bucket);
	public function hasKey(string $bucket, string $key);
	
	public function removeBucket(string $bucket);
	public function removeKey(string $bucket, string $key);

	/**
	 * @param string $bucket
	 * @return array|null
	 */
	public function getBucketIfExists(string $bucket);

	/**
	 * @param string $bucket
	 * @param string $key
	 * @return Data|null
	 */
	public function getItemIfExists(string $bucket, string $key);
	
	public function setItem(Data $item); 
}