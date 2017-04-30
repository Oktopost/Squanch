<?php
namespace Squanch\Plugins\LruCache\Command;


use Squanch\Plugins\LRUCache\LRUCachePlugin;


class HasTest extends \PHPUnit_Framework_TestCase
{
	public function test_onCheck_KeyExists_ReturnTrue()
	{
		$lruCache = new LRUCachePlugin(10);
		$lruCache->set('a', null, 'a')->save();

		self::assertTrue($lruCache->has()->byBucket('a')->byKey('a')->check());
	}
	
	public function test_onCheck_KeyNotExists_ReturnFalse()
	{
		$lruCache = new LRUCachePlugin(10);

		self::assertFalse($lruCache->has()->byBucket('a')->byKey('a')->check());
	}
	
	public function test_onCheck_KeyExistsInDifferentBucket_ReturnFalse()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'b')->save();
		
		self::assertFalse($lruCache->has()->byBucket('a')->byKey('a')->check());
	}
	
	public function test_onUpdateTTL_ItemFound_TTLUpdated()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();
		
		$lruCache->has()->byBucket('a')->byKey('a')->resetTTL(111)->check();
		
		self::assertEquals(111, $lruCache->get()->byBucket('a')->byKey('a')->asData()->TTL);
	}
}