<?php
namespace Squanch\Command;


use dummyStorage\Config;
use PHPUnit_Framework_TestCase;
use Squanch\Base\ICachePlugin;
use Squanch\Enum\TTL;


require_once __DIR__.'/../../dummyStorage/Config.php';


class SetTest extends PHPUnit_Framework_TestCase
{
	/** @var ICachePlugin */
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
		$this->cache->delete('a')->execute();
	}
	
	public function test_onSetSuccess_return_true()
	{
		$key = uniqid();
		$result = false;
		$this->cache->set($key, 'b')->onSuccess(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
		$this->cache->delete($key)->execute();
	}
	
	public function test_onSetSuccess_on_update_reutrn_true()
	{
		$result = false;
		$this->cache->set('a', 'b')->execute();
		$this->cache->set('a', 'c')->onSuccess(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
		$this->cache->delete('a')->execute();
	}
	
	public function test_onSetFail_return_true()
	{
		$result = false;
		
		$this->cache->set('a', 'b')->execute();
		
		$this->cache->set('a', 'b')->insertOnly()->onFail(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
		$this->cache->delete('a')->execute();
	}
	
	public function test_onSet_return_true()
	{
		$result = false;
		$this->cache->set('a', 'b')->onComplete(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
		$this->cache->delete('a')->execute();
	}
	
	public function test_insertOnly_failed_to_update()
	{
		$this->cache->set('a', 'b')->execute();
		$result = $this->cache->set('a', 'c')->insertOnly()->execute();
		
		self::assertFalse($result);
		$this->cache->delete('a')->execute();
	}
	
	public function test_updateOnly_failed_to_insert()
	{
		$key = uniqid();
		$result = $this->cache->set($key, 'b')->updateOnly()->execute();
		self::assertFalse($result);
		$this->cache->delete($key)->execute();
	}
	
	public function test_setForever_return_true()
	{
		$key = uniqId();
		$this->cache->set($key, 'data')->setForever()->execute();
		
		$result = $this->cache->get($key)->asData();
		$interval = TTL::FOREVER;
		
		self::assertLessThan(0, $result->TTL);
		self::assertEquals($result->Created->modify("+ {$interval} seconds"), $result->EndDate);
		$this->cache->delete($key)->execute();
	}
	
	public function test_setTTL_return_true()
	{
		$interval = 10;
		$key = uniqid();
		$this->cache->set($key, 'data')->setTTL($interval)->execute();
		
		$result = $this->cache->get($key)->asData();
		
		self::assertEquals($interval, $result->TTL);
		self::assertEquals((new \DateTime())->modify("+ {$interval} seconds"), $result->EndDate);
		$this->cache->delete($key)->execute();
	}
	
	public function test_use_two_buckets()
	{
		$key = uniqid();
		$success = [
			false, false
		];
		
		$setA = $this->cache->set($key, 'a', 'a')->insertOnly()->onSuccess(function() use (&$success){
			$success[0] = true;
		})->execute();
		
		$setB = $this->cache->set($key, 'a', 'b')->insertOnly()->onSuccess(function() use (&$success){
			$success[1] = true;
		})->execute();
		
		self::assertTrue($setA);
		self::assertTrue($setB);
		self::assertEquals([true, true], $success);
		
		$this->cache->delete($key, 'a')->execute();
		$this->cache->delete($key, 'b')->execute();
	}
}
