<?php
namespace Squanch\Command;


use dummyStorage\Config;
use PHPUnit_Framework_TestCase;
use Squanch\Base\IPlugin;
use Squanch\Objects\Data;


class SetTest extends PHPUnit_Framework_TestCase
{
	/** @var IPlugin */
	private $cache;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->cache = (new Config())->getPlugin();
	}
	
	
	public function test_set_return_true()
	{
		$set = $this->cache->set('a', 'b')->execute();
		self::assertTrue($set);
	}
	
	public function test_onSetSuccess_return_true()
	{
		$result = false;
		$this->cache->set('a', 'b')->onSuccess(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
	
	public function test_onSetFail_return_true()
	{
		$result = false;
		
		$this->cache->set('a', 'b')->execute();
		
		$this->cache->set('a', 'b')->insertOnly()->onFail(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
	
	public function test_onSet_return_true()
	{
		$result = false;
		$this->cache->set('a', 'b')->onComplete(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
	
	public function test_insertOnly_failed_to_update()
	{
		$this->cache->set('a', 'b')->execute();
		$result = $this->cache->set('a', 'c')->insertOnly()->execute();
		
		self::assertFalse($result);
	}
	
	public function test_updateOnly_failed_to_insert()
	{
		$result = $this->cache->set(uniqid(), 'b')->updateOnly()->execute();
		self::assertFalse($result);
	}
	
	public function test_setForever_return_true()
	{
		$key = uniqId();
		$this->cache->set($key, 'data')->setForever()->execute();
		
		$result = $this->cache->get($key)->asData();
		$interval = Data::FOREVER_IN_SEC;
		
		self::assertLessThan(0, $result->TTL);
		self::assertEquals((new \DateTime())->modify("+ {$interval} seconds"), $result->EndDate);
	}
}
