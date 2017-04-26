<?php
namespace Squanch;


use Squanch\Base\ICachePlugin;
use dummyStorage\Config;


class MigrationTest extends \PHPUnit_Framework_TestCase
{
	/** @var ICachePlugin */
	private $migrationPlugin;
	
	/** @var ICachePlugin */
	private $squidHardCachePlugin;
	
	/** @var ICachePlugin */
	private $squidSoftCachePlugin;
	
	
	private function checkInstance()
	{
		return getenv('instance') == 'migration' ? true : false;
	}
	
	protected function setUp()
	{
		$allPlugins = (new Config())->getAllPlugins();
		$this->migrationPlugin = $allPlugins['migration'];
		$this->squidHardCachePlugin = $allPlugins['squid'];
		$this->squidSoftCachePlugin = $allPlugins['predis'];
	}
	
	public function test_migration()
	{
		if (!$this->checkInstance())
		{
			return;
		}
		
		$key = uniqid();
		
		$set = $this->migrationPlugin->set($key, 'a')->save();
		$getHard = $this->squidHardCachePlugin->get($key)->asString();
		$getSoft = $this->squidSoftCachePlugin->get($key)->asString();
		
		self::assertTrue($set);
		self::assertEquals('a', $getHard);
		self::assertFalse($getSoft);
		
		$this->migrationPlugin->delete($key)->execute();
	}
	
	public function test_migration_fallback_return_true()
	{
		if (!$this->checkInstance())
		{
			return;
		}
		
		$key = uniqid();
		$value = 'blahblahblah';
		
		$this->squidSoftCachePlugin->set($key, $value)->save();
		$get = $this->migrationPlugin->get($key);
		
		self::assertEquals($value, $get->asString());
		
		$this->squidSoftCachePlugin->delete($key)->execute();
		$this->migrationPlugin->delete($key)->execute();
	}
}
