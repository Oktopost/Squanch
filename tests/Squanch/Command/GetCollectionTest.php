<?php
namespace Squanch\Command;

use Objection\LiteObject;
use Objection\LiteSetup;
use PHPUnit_Framework_TestCase;
use Squanch\Base\ICachePlugin;
use dummyStorage\Config;
use Squanch\Objects\Data;


require_once __DIR__.'/../../dummyStorage/Config.php';


class GetCollectionTest extends PHPUnit_Framework_TestCase
{
	/** @var ICachePlugin */
	private $cache;
	
	
	private function checkInstance()
	{
		$instance = getenv('instance');
		return $instance != 'redis' && $instance != 'migration';
	}
	
	protected function setUp()
	{
		parent::setUp();
		$this->cache = (new Config())->getPlugin();
	}
	
	
	public function test_get_collection()
	{
		if (!$this->checkInstance())
			return;
		
		$bucket = uniqid();
		$key1 = uniqid();
		$key2 = uniqid();
		$key3 = uniqid();
		
		$this->cache->set($key1, uniqid(), $bucket)->execute();
		$this->cache->set($key2, uniqid(), $bucket)->execute();
		$this->cache->set($key3, uniqid(), $bucket)->execute();
		
		$collection = $this->cache->get()->byBucket($bucket)->asCollection();
		
		$result = $collection->asArrayOfData();
		self::assertCount(3, $result);
	}
	
	public function test_get_asArrayOfData_return_data()
	{
		if (!$this->checkInstance())
			return;
		
		$bucket = uniqId();
		
		$this->cache->set('a', 'b', $bucket)->execute();
		$this->cache->set('c', 'd', $bucket)->execute();
		
		$get = $this->cache->get()->byBucket($bucket)->asCollection()->asArrayOfData();
		
		self::assertCount(2, $get);
		self::assertInstanceOf(Data::class, $get[0]);
		
		$this->cache->delete('a', $bucket)->execute();
		$this->cache->delete('c', $bucket)->execute();
		$this->cache->delete()->byBucket($bucket)->execute();
	}
	
	public function test_get_by_fake_bucket_return_empty_array()
	{
		if (!$this->checkInstance())
			return;
		
		$get = $this->cache->get()->byBucket('fake')->asCollection()->asArrays();
		self::assertEmpty($get);
	}
	
	public function test_resetTTL_will_update_ttl()
	{
		if (!$this->checkInstance())
			return;
		
		$key = uniqid();
		$key2 = uniqid();
		$bucket = uniqid();
		$this->cache->set($key, 'b', $bucket)->setTTL(10)->execute();
		$this->cache->set($key2, 'b', $bucket)->setTTL(10)->execute();
		
		$get = $this->cache->get()->byBucket($bucket)->asCollection()->asArrayOfData();
		self::assertLessThanOrEqual(10, $get[0]->TTL);
		
		$updated = $this->cache->get()->byBucket($bucket)->resetTTL(9999)->asCollection()->asArrayOfData();
		
		self::assertTrue($updated[0]->TTL === 9999);
		$this->cache->delete()->byBucket($bucket)->execute();
	}
	
	public function test_asArrays_return_array()
	{
		if (!$this->checkInstance())
			return;
		
		$key = uniqid();
		$bucket = uniqid();
		$data = ['test'];
		$this->cache->set($key, $data, $bucket)->execute();
		$get = $this->cache->get()->byBucket($bucket)->asCollection()->asArrays();
		
		self::assertTrue(is_array($get));
		self::assertTrue(is_array($get[0]));
		self::assertEquals($data, $get[0]);
		
		$this->cache->delete()->byBucket($bucket)->execute();
	}
	
	public function test_as_Objects_return_objects()
	{
		if (!$this->checkInstance())
			return;
		
		$key = uniqid();
		$bucket = uniqid();
		$this->cache->set($key, (object)['a'=>1], $bucket)->execute();
		$get = $this->cache->get()->byBucket($bucket)->asCollection()->asObjects();
		
		self::assertTrue(is_object($get[0]));
		self::assertEquals(1, $get[0]->a);
		$this->cache->delete()->byBucket($bucket)->execute();
	}
	
	public function test_as_Strings_return_strings()
	{
		if (!$this->checkInstance())
			return;
		
		$key = uniqId();
		$bucket = uniqid();
		$string = 'Lorem ipsum';
		
		$this->cache->set($key, $string, $bucket)->execute();
		$result = $this->cache->get()->byBucket($bucket)->asCollection()->asStrings();
		
		self::assertTrue(is_string($result[0]));
		self::assertEquals($string, $result[0]);
		$this->cache->delete()->byBucket($bucket)->execute();
	}
	
	public function test_as_LiteObject_return_LiteObject()
	{
		if (!$this->checkInstance())
			return;
		
		$key = uniqid();
		$bucket = uniqid();
		$nested = new myOtherObject();
		$nested->a = 'b';
		$obj = new myObject();
		$obj->Some = 'string';
		$obj->Nested = $nested;
		
		$this->cache->set($key, $obj, $bucket)->execute();
		
		$get = $this->cache->get()->byBucket($bucket)->asCollection()->asLiteObjects(myObject::class);
		
		self::assertInstanceOf(myObject::class, $get[0]);
		self::assertEquals('string', $get[0]->Some);
		self::assertEquals('b',$get[0]->Nested->a);
		$this->cache->delete()->byBucket($bucket)->execute();
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