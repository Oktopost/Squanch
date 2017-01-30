<?php
namespace Squanch\Command;



use dummyStorage\Config;
use PHPUnit_Framework_TestCase;
use Squanch\Base\IPlugin;


class DeleteTest extends PHPUnit_Framework_TestCase
{
	/** @var IPlugin */
	private $cache;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->cache = (new Config())->getImplementer();
	}
	
	
	public function test_delete_return_true()
	{
		$this->cache->set('a', 'b')->execute();
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
		$this->cache->set('a', 'b')->execute();
		
		$this->cache->delete('a')->onSuccess(function() use(&$result){
			$result = true;
		})->execute();
		
		self::assertTrue($result);
	}
	
	public function test_onDeleteFail_return_true()
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
}
