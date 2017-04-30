<?php
namespace Squanch\Plugins\LRUCache\Command;


use Squanch\Plugins\LRUCache\LRUCachePlugin;


class SetTest extends \PHPUnit_Framework_TestCase
{
	public function test_onInsert_ItemExists_ReturnFalse()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();
		
		self::assertFalse($lruCache->set()->setBucket('a')->setKey('a')->insert());
	}
	
	public function test_onInsert_ItemExists_ItemNotUpdated()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();
		
		$lruCache->set()->setBucket('a')->setKey('a')->setTTL(12)->insert();
		
		self::assertNotEquals(12, $lruCache->get()->byBucket('a')->byKey('a')->asData()->TTL);
	}
	
	public function test_onInsert_ItemNotExists_ReturnTrue()
	{
		$lruCache = new LRUCachePlugin(10);
		
		self::assertTrue($lruCache->set()->setBucket('a')->setKey('a')->insert());
	}
	
	public function test_onInsert_ItemNotExists_ItemInserted()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set()->setBucket('a')->setKey('a')->insert();
		
		self::assertTrue($lruCache->has()->byBucket('a')->byKey('a')->check());
	}
		
	public function test_onInsert_ItemNotExistsInFullLRUCache_ItemInserted()
	{
		$lruCache = new LRUCachePlugin(2);
		
		$lruCache->set('a', null, 'a')->save();
		$lruCache->set('b', null, 'b')->save();
		$lruCache->set('c', null, 'c')->save();
		
		self::assertTrue($lruCache->has()->byBucket('c')->byKey('c')->check());
	}
	
	public function test_onInsert_MostUnusedItemExistsInFullCache_MostUnusedItemRemoved()
	{
		$lruCache = new LRUCachePlugin(2);
		
		$lruCache->set('a', null, 'a')->save();
		$lruCache->set('b', null, 'b')->save();
		
		$lruCache->get()->byBucket('a')->byKey('a')->asData();
		
		$lruCache->set('c', null, 'c')->save();
		
		self::assertFalse($lruCache->has()->byBucket('b')->byKey('b')->check());
	}
	
	public function test_onUpdate_ItemExists_ReturnTrue()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();

		self::assertTrue($lruCache->set()->setBucket('a')->setKey('a')->update());
	}
	
	public function test_onUpdate_ItemExists_ItemUpdated()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();

		$lruCache->set()->setBucket('a')->setKey('a')->setTTL(12)->update();
		
		self::assertEquals(12, $lruCache->get()->byBucket('a')->byKey('a')->asData()->TTL);
	}
	
	public function test_onUpdate_ItemNotExists_ReturnFalse()
	{
		$lruCache = new LRUCachePlugin(10);
		
		self::assertFalse($lruCache->set()->setBucket('a')->setKey('a')->update());
	}
	
	public function test_onUpdate_ItemNotExists_ItemNotInserted()
	{
		$lruCache = new LRUCachePlugin(10);

		$lruCache->set()->setBucket('a')->setKey('a')->update();
		
		self::assertFalse($lruCache->has()->byBucket('a')->byKey('a')->check());
	}
	
	public function test_onSave_ItemExists_ItemUpdated()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();

		$lruCache->set()->setBucket('a')->setKey('a')->setTTL(12)->save();
		
		self::assertEquals(12, $lruCache->get()->byBucket('a')->byKey('a')->asData()->TTL);
	}
	
	public function test_onSave_ItemNotExists_ItemInserted()
	{
		$lruCache = new LRUCachePlugin(10);

		$lruCache->set()->setBucket('a')->setKey('a')->save();
		
		self::assertTrue($lruCache->has()->byBucket('a')->byKey('a')->check());
	}
	
	public function test_onSave_ItemExists_ReturnTrue()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();
		
		self::assertTrue($lruCache->set()->setBucket('a')->setKey('a')->save());
	}
	
	public function test_onSave_ItemNotExists_ReturnTrue()
	{
		$lruCache = new LRUCachePlugin(10);

		self::assertTrue($lruCache->set()->setBucket('a')->setKey('a')->save());
	}
}