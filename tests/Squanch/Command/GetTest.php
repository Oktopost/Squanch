<?php
namespace Squanch\Command;

use Objection\LiteObject;
use Objection\LiteSetup;
use PHPUnit_Framework_TestCase;
use Squanch\Base\IPlugin;
use dummyStorage\Config;
use Squanch\Objects\Data;


require_once __DIR__.'/../../dummyStorage/Config.php';


class GetTest extends PHPUnit_Framework_TestCase
{
	/** @var IPlugin */
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
	}
	
	public function test_asArray_return_array()
	{
		$this->cache->set('a', ['test'])->execute();
		$get = $this->cache->get()->byKey('a')->asArray();

		self::assertTrue(is_array($get));
	}
	
	public function test_as_Object_return_object()
	{
		$this->cache->set('a', (object)['a'=>1])->execute();
		$get = $this->cache->get('a')->asObject();
		
		self::assertTrue(is_object($get));
	}
	
	public function test_as_LiteObject_return_LiteObject()
	{
		$obj = new myObject();
		$this->cache->set('a', $obj)->execute();
		
		$get = $this->cache->get('a')->asLiteObject(myObject::class);
		
		self::assertInstanceOf(myObject::class, $get);
	}
}


class myObject extends LiteObject
{
	protected function _setup()
	{
		return [
			'some' => LiteSetup::createString('thing')
		];
	}
}