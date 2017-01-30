<?php
namespace Squanch\Command;


use dummyStorage\Config;
use PHPUnit_Framework_TestCase;
use Squanch\Base\IPlugin;


class HasTest extends PHPUnit_Framework_TestCase
{
	/** @var IPlugin */
	private $cache;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->cache = (new Config())->getImplementer();
	}
	
	
	public function test_has_return_true()
	{
		$this->cache->set('a', 'b')->execute();
		
		self::assertTrue($this->cache->has('a')->execute());
	}
	
	public function test_has_return_false()
	{
		self::assertFalse($this->cache->has(uniqid())->execute());
	}
	
	public function test_resetTTL_resets_ttl()
	{
		$this->cache->set('a', 'b')->execute();
		$this->cache->has('a')->resetTTL(60)->execute();
		
		$data = $this->cache->get('a')->asData();
		self::assertLessThanOrEqual(60, $data->TTL);
	}
	
	public function test_onHasSuccess_return_true()
	{
		$result = false;
		$this->cache->set('a', 'b')->execute();
		
		$this->cache->has('a')->onSuccess(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
	
	public function test_onHasFail_return_true()
	{
		$result = false;
		
		$this->cache->has('fake')->onFail(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
	
	public function test_onHas_return_true()
	{
		$result = false;
		$this->cache->has('a')->onComplete(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
}
