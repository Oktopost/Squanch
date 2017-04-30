<?php
namespace Squanch\Plugins\LRUCache\Command;


use Squanch\Exceptions\OperationNotSupportedOnBucketException;
use Squanch\Plugins\LRUCache\LRUCachePlugin;


class DeleteTest extends \PHPUnit_Framework_TestCase
{	
	public function test_onDeleteBucket_BucketNotFound_ReturnFalse()
	{
		$lruCache = new LRUCachePlugin(10);
		
		try 
		{
			self::assertFalse($lruCache->delete()->byBucket('a')->execute());
		}
		catch (OperationNotSupportedOnBucketException $e)
		{
			return;
		}
		
	}
	
	public function test_onDeleteBucket_BucketFound_ReturnTrue()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();

		try 
		{
			self::assertTrue($lruCache->delete()->byBucket('a')->execute());
		}
		catch (OperationNotSupportedOnBucketException $e)
		{
			return;
		}
	}
	
	public function test_onDeleteBucket_BucketFound_BucketRemoved()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();

		try 
		{
			$lruCache->delete()->byBucket('a')->execute();
		
			self::assertFalse($lruCache->has()->byBucket('a')->check());
		}
		catch (OperationNotSupportedOnBucketException $e)
		{
			return;
		}
	}
	
	public function test_onDeleteBucket_DifferentBucketExists_DifferentBucketNotRemoved()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$lruCache->set('a', null, 'a')->save();
		$lruCache->set('b', null, 'b')->save();

		try 
		{
			$lruCache->delete()->byBucket('a')->execute();
		
			self::assertTrue($lruCache->has()->byBucket('b')->check());
		}
		catch (OperationNotSupportedOnBucketException $e)
		{
			return;
		}
	}
	
	public function test_onDeleteItem_BucketNotFound_ReturnFalse()
	{
		$lruCache = new LRUCachePlugin(10);

		self::assertFalse($lruCache->delete()->byBucket('a')->byKey('a')->execute());
	}
	
	public function test_onDeleteItem_ItemNotFound_ReturnFalse()
	{
		$lruCache = new LRUCachePlugin(10);
		$lruCache->set('b', null, 'a')->save();
		
		self::assertFalse($lruCache->delete()->byBucket('a')->byKey('a')->execute());
	}
	
	public function test_onDeleteItem_ItemFound_ReturnTrue()
	{
		$lruCache = new LRUCachePlugin(10);
		$lruCache->set('a', null, 'a')->save();
		
		self::assertTrue($lruCache->delete()->byBucket('a')->byKey('a')->execute());
	}
	
	public function test_onDeleteItem_ItemFound_ItemRemoved()
	{
		$lruCache = new LRUCachePlugin(10);
		$lruCache->set('a', null, 'a')->save();

		$lruCache->delete()->byBucket('a')->byKey('a')->execute();
		
		self::assertFalse($lruCache->has()->byBucket('a')->byKey('a')->check());
	}
	
	public function test_onDeleteItem_DifferentItemExists_DifferentItemNotRemoved()
	{
		$lruCache = new LRUCachePlugin(10);
		$lruCache->set('a', null, 'a')->save();
		$lruCache->set('b', null, 'a')->save();
		
		$lruCache->delete()->byBucket('a')->byKey('a')->execute();
		
		self::assertTrue($lruCache->has()->byBucket('a')->byKey('b')->check());
	}
	
	public function test_onDeleteItem_DifferentBucketExists_DifferentItemNotRemoved()
	{
		$lruCache = new LRUCachePlugin(10);
		$lruCache->set('a', null, 'a')->save();
		$lruCache->set('a', null, 'b')->save();
		
		$lruCache->delete()->byBucket('a')->byKey('a')->execute();
		
		self::assertTrue($lruCache->has()->byBucket('b')->byKey('a')->check());
	}
}