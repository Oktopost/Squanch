<?php
namespace Squanch\Command;


use Squanch\Objects\Data;

use Objection\LiteSetup;
use Objection\LiteObject;


class GetTest extends \PHPUnit_Framework_TestCase
{
	use \Squanch\TSanityTest;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->loadPlugin();
	}
	
	
	public function test_get_asData_return_data()
	{
		$this->cache->set('a', 'b')->save();
		
		$get = $this->cache->get()->byKey('a')->asData();
		
		self::assertInstanceOf(Data::class, $get);
		$this->cache->delete('a')->execute();
	}
	
	public function test_get_by_key_return_null()
	{
		$get = $this->cache->get()->byKey('fake');
		self::assertNull($get->asData());
	}
	
	public function test_resetTTL_will_update_ttl()
	{
		$key = uniqid();
		$this->cache->set($key, 'b')->setTTL(10)->save();
		
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
		$this->cache->set($key, $data)->save();
		$get = $this->cache->get()->byKey($key)->asArray();

		self::assertTrue(is_array($get));
		self::assertEquals($data, $get);
		$this->cache->delete($key)->execute();
	}
	
	public function test_as_Object_return_object()
	{
		$key = uniqid();
		$this->cache->set($key, (object)['a' => 1])->save();
		$get = $this->cache->get($key)->asObject();
		
		self::assertTrue(is_object($get));
		self::assertEquals(1, $get->a);
		$this->cache->delete($key)->execute();
	}
	
	public function test_as_String_return_string()
	{
		$key = uniqId();
		$string = 'Lorem ipsum';
		
		$this->cache->set($key, $string)->save();
		$result = $this->cache->get($key)->asString();
		
		self::assertTrue(is_string($result));
		self::assertEquals($string, $result);
		$this->cache->delete($key)->execute();
	}
	
	public function test_as_LiteObject_return_LiteObject()
	{
		$key = uniqid();
		
		$nested = new myOtherMegaObject();
		$nested->a = 'b';
		$obj = new myMegaObject();
		$obj->Some = 'string';
		$obj->Nested = $nested;
		
		$this->cache->set($key, $obj)->save();
		
		$get = $this->cache->get($key)->asLiteObject(myMegaObject::class);
		
		self::assertInstanceOf(myMegaObject::class, $get);
		self::assertEquals($get->Some, 'string');
		self::assertEquals($get->Nested->a, 'b');
		$this->cache->delete($key)->execute();
	}
	
	public function test_as_array_of_LiteObjects()
	{
		$key = uniqid();
		$obj = new myOtherMegaObject();
		$obj->a = 'b';
		$anotherObj = new $obj();
		$anotherObj->a = 'c';
		
		$array = [$obj, $anotherObj];
		
		$this->cache->set($key, $array)->save();
		
		$get = $this->cache->get($key)->asLiteObjects(myOtherMegaObject::class);
		
		foreach ($get as $key => $value)
		{
			self::assertInstanceOf(myOtherMegaObject::class, $value);
		}
		
		self::assertEquals('b', $get[0]->a);
		self::assertEquals('c', $get[1]->a);
		
		$this->cache->delete($key)->execute();
		
	}
	
	public function test_use_two_buckets()
	{
		$key = uniqid();
		$this->cache->set($key, 'a', 'b')->insert();
		$this->cache->set($key, 'c', 'd')->insert();
		
		$getA = $this->cache->get($key, 'b')->asString();
		$getB = $this->cache->get($key, 'd')->asString();
		
		self::assertEquals('a', $getA);
		self::assertEquals('c', $getB);
		
		$this->cache->delete($key, 'b')->execute();
		$this->cache->delete($key, 'd')->execute();
	}
}

class myMegaObject extends LiteObject
{
	protected function _setup()
	{
		return [
			'Some' => LiteSetup::createString(),
			'Nested' => LiteSetup::createInstanceOf(myOtherMegaObject::class)
		];
	}
}

class myOtherMegaObject extends LiteObject
{
	protected function _setup()
	{
		return [
			'a' => LiteSetup::createString()
		];
	}
}