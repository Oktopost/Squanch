<?php
namespace Squanch\Plugins\Fallback\Command;


use Squanch\Plugins\Fallback\FallbackPlugin;
use Squanch\Plugins\InMemoryCache\InMemoryPlugin;


class GetTest extends \PHPUnit_Framework_TestCase
{
	public function test_onGet_ItemNotFound_ReturnFalse()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		
		$get = (new FallbackPlugin([$fallback1, $fallback2]))->get('a', 'buck');
		
		self::assertNull($get->asData());
	}
	
	public function test_onCheck_ItemFoundInLastPluginOnly_ReturnTrue()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback2 = new InMemoryPlugin();
		$fallback2->set('a', 'data', 'buck')->save();
		
		$get = (new FallbackPlugin([$fallback1, $fallback2]))->get('a', 'buck');
		
		self::assertNotNull($get->asData());
	}
	
	public function test_onCheck_ItemFoundInFirstPluginOnly_ReturnTrue()
	{
		$fallback1 = new InMemoryPlugin();
		$fallback1->set('a', 'data', 'buck')->save();
		$fallback2 = new InMemoryPlugin();
		
		$get = (new FallbackPlugin([$fallback1, $fallback2]))->get('a', 'buck');
		
		self::assertNotNull($get->asData());
	}
}