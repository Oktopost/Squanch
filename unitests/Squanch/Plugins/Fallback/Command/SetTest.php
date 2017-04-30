<?php
namespace Squanch\Plugins\Fallback\Command;


use Squanch\Plugins\Fallback\FallbackPlugin;
use Squanch\Plugins\InMemoryCache\InMemoryPlugin;


class SetTest extends \PHPUnit_Framework_TestCase
{
	public function test_onInsert_AllFallbacksReturnedFalse_ReturnFalse()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'data', 'buck')->save();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'data', 'buck')->save();
		
		$set = (new FallbackPlugin([$fallback1, $fallback2]))->set('a', 'data', 'buck');
		
		self::assertFalse($set->insert());
	}
	
	public function test_onInsert_AtLeastOneReturnedTrue_ReturnTrue()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'data', 'buck')->save();
		
		$set = (new FallbackPlugin([$fallback1, $fallback2]))->set('a', 'data', 'buck');
		
		self::assertTrue($set->insert());
	}
	
	public function test_onInsert_ObjectInsertedIntoAll()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		
		(new FallbackPlugin([$fallback1, $fallback2]))->set('a', 'data', 'buck')->insert();
		
		self::assertTrue($fallback1->has('a', 'buck')->check());
		self::assertTrue($fallback2->has('a', 'buck')->check());
	}
	
	public function test_onInsert_ObjectNotUpdated()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'existing_data', 'buck')->save();
		$fallback2 = new InMemoryPlugin();
		
		(new FallbackPlugin([$fallback1, $fallback2]))->set('a', 'data', 'buck')->insert();
		
		self::assertEquals('existing_data', $fallback1->get('a', 'buck')->asString());
	}
	
	
	public function test_onUpdate_AllFallbacksReturnedFalse_ReturnFalse()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		
		$set = (new FallbackPlugin([$fallback1, $fallback2]))->set('a', 'data', 'buck');
		
		self::assertFalse($set->update());
	}
	
	public function test_onUpdate_AtLeastOneReturnedTrue_ReturnTrue()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'data', 'buck')->save();
		$fallback2 = new InMemoryPlugin();
		
		$set = (new FallbackPlugin([$fallback1, $fallback2]))->set('a', 'data', 'buck');
		
		self::assertTrue($set->update());
	}
	
	public function test_onUpdate_ObjectUpdatedIntoAll()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'dataA', 'buck')->save();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'dataA', 'buck')->save();
		
		(new FallbackPlugin([$fallback1, $fallback2]))->set('a', 'dataB', 'buck')->update();
		
		self::assertEquals('dataB', $fallback1->get('a', 'buck')->asString());
		self::assertEquals('dataB', $fallback2->get('a', 'buck')->asString());
	}
	
	public function test_onUpdate_ObjectNotInserted()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		
		(new FallbackPlugin([$fallback1, $fallback2]))->set('a', 'data', 'buck')->update();
		
		self::assertFalse($fallback1->has('a', 'buck')->check());
		self::assertFalse($fallback2->has('a', 'buck')->check());
	}
	
	
	public function test_onSave_ObjectsUpdated()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'dataA', 'buck')->save();
		
		(new FallbackPlugin([$fallback1]))->set('a', 'dataB', 'buck')->save();
		
		self::assertEquals('dataB', $fallback1->get('a', 'buck')->asString());
	}
	
	public function test_onSave_ObjectsInserted()
	{
		$fallback1 = new InMemoryPlugin();
		
		(new FallbackPlugin([$fallback1]))->set('a', 'dataB', 'buck')->save();
		
		self::assertEquals('dataB', $fallback1->get('a', 'buck')->asString());
	}
}