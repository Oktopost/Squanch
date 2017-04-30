<?php
namespace Squanch\Plugins\LRUCache\Base;


use Squanch\Objects\Data;


interface ILRUAdapter
{
	public function hasBucket(string $bucket): bool;
	public function hasKey(string $bucket, string $key): bool;
	
	public function removeBucket(string $bucket): bool;
	public function removeKey(string $bucket, string $key): bool;

	/**
	 * @param string $bucket
	 * @param string $key
	 * @return Data|null
	 */
	public function getItemIfExists(string $bucket, string $key);
	
	public function setItem(Data $item): bool; 
	
	public function createItem(Data $item): bool;
	
	public function updateItem(Data $item): bool;
	
	public function setTTL(string $bucket, string $key, int $ttl);
}