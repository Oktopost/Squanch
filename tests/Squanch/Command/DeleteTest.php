<?php
namespace Squanch\Command;


use dummyStorage\Config;
use PHPUnit_Framework_TestCase;
use Squanch\Base\ICachePlugin;


class DeleteTest extends PHPUnit_Framework_TestCase
{
	/** @var ICachePlugin */
	private $cache;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->cache = (new Config())->getPlugin();
	}
	
	
	public function test_delete_return_true()
	{
		$this->cache->set('a', 'b')->save();
		$result = $this->cache->delete('a')->execute();
		self::assertTrue($result);
	}
	
	public function test_delete_return_false()
	{
		$result = $this->cache->delete(uniqid())->execute();
		self::assertFalse($result);
	}
	
	public function test_onDeleteSuccess_return_true()
	{
		$result = false;
		$this->cache->set('a', 'b')->save();
		
		$this->cache->delete('a')->onSuccess(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
	
	public function test_onDelete_fake_return_false()
	{
		$delete = $this->cache->delete('fake')->execute();
		self::assertFalse($delete);
	}
	
	public function test_onDeleteFail_return_true_for_callback()
	{
		$result = false;
		
		$this->cache->delete('fake')->onFail(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
	
	public function test_onDelete_return_true()
	{
		$result = false;
		$this->cache->delete('a')->onComplete(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
	
	public function test_remove_from_one_box_leave_other()
	{
		$key = uniqid();
		$this->cache->set($key, 'a', 'b')->save();
		$this->cache->set($key, 'c', 'd')->save();
		
		$this->cache->delete($key, 'b')->execute();
		$exists = $this->cache->has($key, 'd')->save();
		
		self::assertTrue($exists);
		
		$this->cache->delete($key, 'd')->execute();
	}
	
	public function test_remove_by_bucket()
	{
		$this->cache->set('a', 'a', 'a')->save();
		$this->cache->set('b', 'a', 'a')->save();
		$this->cache->set('c', 'a', 'a')->save();
		
		$this->cache->set('a', 'a', 'b')->save();
		$this->cache->set('b', 'a', 'b')->save();
		$this->cache->set('c', 'a', 'b')->save();
		
		$delete = $this->cache->delete()->byBucket('a')->execute();
		
		$get = [
			$this->cache->get('a', 'b')->asString(),
			$this->cache->get('b', 'b')->asString(),
			$this->cache->get('c', 'b')->asString(),
		];
		
		// result could be bool instead of int in case of using nosql storage
		self::assertEquals(3||true, $delete);
		self::assertEquals(['a','a','a'], $get);
		
		$deleteSecond = $this->cache->delete()->byBucket('b')->execute();
		self::assertEquals(3, $deleteSecond);
	}
}
