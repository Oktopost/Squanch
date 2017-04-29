<?php
namespace Squanch\Plugins\InMemoryCache\Base;


use Squanch\Objects\Data;


interface IStorage
{
	public function storage(): array;
	
	public function hasBucket(string $bucket): bool;
	public function hasKey(string $bucket, string $key): bool;
	
	public function removeBucket(string $bucket): bool;
	public function removeKey(string $bucket, string $key): bool;

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
	
	public function setItem(Data $item): bool; 
}