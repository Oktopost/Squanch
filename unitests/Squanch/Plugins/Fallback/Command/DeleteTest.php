<?php
namespace Squanch\Plugins\Fallback\Command;


use Squanch\Plugins\Fallback\FallbackPlugin;
use Squanch\Plugins\InMemoryCache\InMemoryPlugin;


class DeleteTest extends \PHPUnit_Framework_TestCase
{
	public function test_onDeleteBucket_DeleteCalledOnAll()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'b', 'buck1')->save();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'b', 'buck1')->save();
		
		$delete = (new FallbackPlugin([$fallback1, $fallback2]))->delete();
		$delete->byBucket('buck1')->execute();
		
		self::assertFalse($fallback1->has('a', 'buck1')->check());
		self::assertFalse($fallback2->has('a', 'buck1')->check());
	}
	
	public function test_onDeleteBucket_OnlyRequestedBucketDeleted()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'b', 'buck_other')->save();
		
		$delete = (new FallbackPlugin([$fallback1]))->delete();
		$delete->byBucket('buck1')->execute();
		
		self::assertTrue($fallback1->has('a', 'buck_other')->check());
	}
	
	public function test_onDeleteBucket_AllFallbacksReturnedFalse_ReturnFalse()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		
		$delete = (new FallbackPlugin([$fallback1, $fallback2]))->delete();
		$delete->byBucket('buck');
		
		self::assertFalse($delete->execute());
	}
	
	public function test_onDeleteBucket_AtLeastOneReturnedTrue_ReturnTrue()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'a', 'buck')->save();
		$fallback3 = new InMemoryPlugin();
		
		$delete = (new FallbackPlugin([$fallback1, $fallback2, $fallback3]))->delete();
		$delete->byBucket('buck');
		
		self::assertTrue($delete->execute());
	}
	
	
	public function test_onDeleteItem_DeleteCalledOnAll()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'b', 'buck1')->save();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'b', 'buck1')->save();
		
		$delete = (new FallbackPlugin([$fallback1, $fallback2]))->delete();
		$delete->byBucket('buck1')->byKey('a')->execute();
		
		self::assertFalse($fallback1->has('a', 'buck1')->check());
		self::assertFalse($fallback2->has('a', 'buck1')->check());
	}
	
	public function test_onDeleteItem_OnlyRequestedItemDeleted()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'b', 'buck1')->save();
		$fallback1->set('b', 'b', 'buck1')->save();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'b', 'buck1')->save();
		
		$delete = (new FallbackPlugin([$fallback1, $fallback2]))->delete();
		$delete->byBucket('buck1')->byKey('a')->execute();
		
		self::assertFalse($fallback1->has('a', 'buck1')->check());
		self::assertTrue($fallback1->has('b', 'buck1')->check());
		self::assertFalse($fallback2->has('a', 'buck1')->check());
	}
	
	public function test_onDeleteItem_AllFallbacksReturnedFalse_ReturnFalse()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		
		$delete = (new FallbackPlugin([$fallback1, $fallback2]))->delete();
		$delete->byBucket('buck')->byKey('n');
		
		self::assertFalse($delete->execute());
	}
	
	public function test_onDeleteItem_AtLeastOneReturnedTrue_ReturnTrue()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'a', 'buck')->save();
		$fallback3 = new InMemoryPlugin();
		
		$delete = (new FallbackPlugin([$fallback1, $fallback2, $fallback3]))->delete();
		$delete->byBucket('buck')->byKey('a');
		
		self::assertTrue($delete->execute());
	}
}