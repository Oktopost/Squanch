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
		$this->cache->set('a', 'b')->setTTL(10)->execute();
		
		$get = $this->cache->get('a')->asData();
		self::assertLessThanOrEqual(10, $get->TTL);
		
		$updated = $this->cache->get()->byKey('a')->resetTTL(9999)->asData();
		
		self::assertTrue($updated->TTL === 9999);
		$this->cache->delete('a')->execute();
	}
	
	public function test_asArray_return_array()
	{
		$this->cache->set('a', ['test'])->execute();
		$get = $this->cache->get()->byKey('a')->asArray();

		self::assertTrue(is_array($get));
		$this->cache->delete('a')->execute();
	}
	
	public function test_as_Object_return_object()
	{
		$this->cache->set('a', (object)['a'=>1])->execute();
		$get = $this->cache->get('a')->asObject();
		
		self::assertTrue(is_object($get));
		self::assertEquals(1, $get->a);
		$this->cache->delete('a')->execute();
	}
	
	public function test_as_LiteObject_return_LiteObject()
	{
		$key = uniqid();
		
		$nested = new myOtherObject();
		$nested->a = 'b';
		$obj = new myObject();
		$obj->some = 'string';
		$obj->nested = $nested;
		
		$this->cache->set($key, $obj)->execute();
		
		$get = $this->cache->get($key)->asLiteObject(myObject::class);
		
		self::assertInstanceOf(myObject::class, $get);
		self::assertEquals($get->some, 'string');
		self::assertEquals($get->nested->a, 'b');
		$this->cache->delete($key)->execute();
	}
}

class myObject extends LiteObject
{
	protected function _setup()
	{
		return [
			'some' => LiteSetup::createString(),
			'nested' => LiteSetup::createInstanceOf(myOtherObject::class)
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