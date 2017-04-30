<?php
namespace Squanch\Plugins\Fallback\Command;


use Squanch\Plugins\Fallback\FallbackPlugin;
use Squanch\Plugins\InMemoryCache\InMemoryPlugin;


class HasTest extends \PHPUnit_Framework_TestCase
{
	public function test_onCheck_ItemNotFound_ReturnFalse()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		
		$has = (new FallbackPlugin([$fallback1, $fallback2]))->has('a', 'buck');
		
		self::assertFalse($has->check());
	}
	
	public function test_onCheck_ItemFoundInLastPluginOnly_ReturnTrue()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'data', 'buck')->save();
		
		$has = (new FallbackPlugin([$fallback1, $fallback2]))->has('a', 'buck');
		
		self::assertTrue($has->check());
	}
	
	public function test_onCheck_ItemFoundInFirstPluginOnly_ReturnTrue()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'data', 'buck')->save();
		$fallback2 = new InMemoryPlugin();
		
		$has = (new FallbackPlugin([$fallback1, $fallback2]))->has('a', 'buck');
		
		self::assertTrue($has->check());
	}
}