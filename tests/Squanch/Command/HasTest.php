<?php
namespace Squanch\Command;


class HasTest extends \PHPUnit_Framework_TestCase
{
	use \Squanch\TSanityTest;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->loadPlugin();
	}
	
	
	public function test_has_return_true()
	{
		$this->cache->set('a', 'b')->save();
		
		self::assertTrue($this->cache->has('a')->check());
		$this->cache->delete('a')->execute();
	}
	
	public function test_has_return_false()
	{
		self::assertFalse($this->cache->has(uniqid())->check());
	}
	
	public function test_resetTTL_resets_ttl()
	{
		$this->cache->set('a', 'b')->save();
		$this->cache->has('a')->resetTTL(60)->check();
		
		$data = $this->cache->get('a')->asData();
		self::assertLessThanOrEqual(60, $data->TTL);
		$this->cache->delete('a')->execute();
	}
	
	public function test_onHasSuccess_return_true()
	{
		$result = false;
		$this->cache->set('a', 'b')->save();
		
		$this->cache->getEvents()->onHas()->onHit(function() use (&$result) {
			$result = true;
		});
		
		$this->cache->has('a')->check();
		
		self::assertTrue($result);
		$this->cache->delete('a')->execute();
	}
	
	public function test_onHasFail_return_true()
	{
		$result = false;
		
		$this->cache->getEvents()->onHas()->onMiss(function() use (&$result) {
			$result = true;
		});
		
		$this->cache->has('a')->check();
		
		self::assertTrue($result);
	}
}
