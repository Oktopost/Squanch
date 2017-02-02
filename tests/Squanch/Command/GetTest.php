<?php
namespace Squanch\Command;

use Objection\LiteObject;
use Objection\LiteSetup;
use PHPUnit_Framework_TestCase;
use Squanch\Base\ICachePlugin;
use dummyStorage\Config;
use Squanch\Objects\Data;


require_once __DIR__.'/../../dummyStorage/Config.php';


class GetTest extends PHPUnit_Framework_TestCase
{
	/** @var ICachePlugin */
	private $cache;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->cache = (new Config())->getPlugin();
	}
	
	
	public function test_get_asData_return_data()
	{
		$this->cache->set('a', 'b')->execute();
		
		$get = $this->cache->get()->byKey('a')->asData();
		
		self::assertInstanceOf(Data::class, $get);
		$this->cache->delete('a')->execute();
	}
	
	public function test_get_by_key_return_false()
	{
		$get = $this->cache->get()->byKey('fake');
		self::assertFalse($get->execute());
	}
	
	public function test_resetTtl_on_will_update_ttl()
	{
		$key = uniqid();
		$this->cache->set($key, 'b')->setTTL(10)->execute();
		
		$get = $this->cache->get($key)->asData();
		self::assertLessThanOrEqual(10, $get->TTL);
		
		$updated = $this->cache->get()->byKey($key)->resetTTL(9999)->asData();
		
		self::assertTrue($updated->TTL === 9999);
		$this->cache->delete($key)->execute();
	}
	
	public function test_asArray_return_array()
	{
		$key = uniqid();
		$data = ['test'];
		$this->cache->set($key, $data)->execute();
		$get = $this->cache->get()->byKey($key)->asArray();

		self::assertTrue(is_array($get));
		self::assertEquals($data, $get);
		$this->cache->delete($key)->execute();
	}
	
	public function test_as_Object_return_object()
	{
		$this->cache->set('a', (object)['a'=>1])->execute();
		$get = $this->cache->get('a')->asObject();
		
		self::assertTrue(is_object($get));
		self::assertEquals(1, $get->a);
		$this->cache->delete('a')->execute();
	}
	
	public function test_as_String_return_string()
	{
		$key = uniqId();
		$string = 'Lorem ipsum';
		
		$this->cache->set($key, $string)->execute();
		$result = $this->cache->get($key)->asString();
		
		self::assertTrue(is_string($result));
		self::assertEquals($string, $result);
		$this->cache->delete($key)->execute();
	}
	
	public function test_as_LiteObject_return_LiteObject()
	{
		$key = uniqid();
		
		$nested = new myOtherObject();
		$nested->a = 'b';
		$obj = new myObject();
		$obj->Some = 'string';
		$obj->Nested = $nested;
		
		$this->cache->set($key, $obj)->execute();
		
		$get = $this->cache->get($key)->asLiteObject(myObject::class);
		
		self::assertInstanceOf(myObject::class, $get);
		self::assertEquals($get->Some, 'string');
		self::assertEquals($get->Nested->a, 'b');
		$this->cache->delete($key)->execute();
	}
	
	public function test_use_two_buckets()
	{
		$key = uniqid();
		$this->cache->set($key, 'a', 'b')->insertOnly()->execute();
		$this->cache->set($key, 'c', 'd')->insertOnly()->execute();
		
		$getA = $this->cache->get($key, 'b')->asString();
		$getB = $this->cache->get($key, 'd')->asString();
		
		self::assertEquals('a', $getA);
		self::assertEquals('c', $getB);
		
		$this->cache->delete($key, 'b')->execute();
		$this->cache->delete($key, 'd')->execute();
	}
	
	public function test_get_onComplete_twice()
	{
		$result = [false, false];
		$this->cache->get('a', 'b')->onComplete(function() use(&$result){
			$result[0] = true;
		})->onComplete(function() use(&$result) {
			$result[1] = true;
		})->execute();
		
		self::assertEquals([true, true], $result);
	}
}

class myObject extends LiteObject
{
	protected function _setup()
	{
		return [
			'Some' => LiteSetup::createString(),
			'Nested' => LiteSetup::createInstanceOf(myOtherObject::class)
		];
	}
}

class myOtherObject extends LiteObject
{
	protected function _setup()
	{
		return [
			'a' => LiteSetup::createString()
		];
	}
}