<?php
namespace Squanch\Command;


use PHPUnit_Framework_TestCase;
use Squanch\Base\ICachePlugin;
use Squanch\Enum\TTL;


class SetTest extends PHPUnit_Framework_TestCase
{
	use \Squanch\TSanityTest;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->loadPlugin();
	}
	
	
	public function test_set_return_true()
	{
		$set = $this->cache->set('a', 'b')->save();
		self::assertTrue($set);
		$this->cache->delete('a')->execute();
	}
	
	public function test_insertOnly_failed_to_update()
	{
		$this->cache->set('a', 'b')->save();
		$result = $this->cache->set('a', 'c')->insert();
		
		self::assertFalse($result);
		$this->cache->delete('a')->execute();
	}
	
	public function test_updateOnly_failed_to_insert()
	{
		$key = uniqid();
		$result = $this->cache->set($key, 'b')->update();
		self::assertFalse($result);
		$this->cache->delete($key)->execute();
	}
	
	public function test_setForever_return_true()
	{
		$key = uniqId();
		$this->cache->set($key, 'data')->setForever()->save();
		
		$result = $this->cache->get($key)->asData();
		$interval = TTL::FOREVER;
		
		self::assertLessThan(0, $result->TTL);
		self::assertGreaterThanOrEqual(TTL::END_OF_TIME, $result->EndDate);
		
		$this->cache->delete($key)->execute();
	}
	
	public function test_setTTL_return_true()
	{
		$interval = 1000;
		$key = uniqid();
		$this->cache->set($key, 'data')->setTTL($interval)->save();
		
		$result = $this->cache->get($key)->asData();
		$t = (new \DateTime())->modify("+ $interval seconds")->getTimestamp();
		
		self::assertEquals($interval, $result->TTL);
		self::assertEquals($t, $result->EndDate->getTimestamp());
		$this->cache->delete($key)->execute();
	}
	
	public function test_use_two_buckets()
	{
		$key = uniqid();
		
		self::assertTrue($this->cache->set($key, 'a', 'a')->insert());
		self::assertTrue($this->cache->set($key, 'a', 'b')->insert());
		
		self::assertTrue($this->cache->has($key, 'a')->check());
		self::assertTrue($this->cache->has($key, 'b')->check());
	}
}
