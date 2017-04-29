<?php
namespace Squanch\Plugins\InMemoryCache\Command;


use Squanch\Base\Callbacks\Events\ISetEvent;
use Squanch\Objects\Data;
use Squanch\Plugins\InMemoryCache\Storage;


class SetTest extends \PHPUnit_Framework_TestCase
{
	public function test_onInsert_ItemExists_ReturnFalse()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		
		self::assertFalse($set->setBucket('a')->setKey('a')->insert());
	}
	
	public function test_onInsert_ItemExists_ItemNotUpdated()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		$set->setBucket('a')->setKey('a')->setTTL(12)->insert();
		
		self::assertNotEquals(12, $storage->getItemIfExists('a', 'a')->TTL);
	}
	
	public function test_onInsert_ItemNotExists_ReturnTrue()
	{
		$storage = new Storage();
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		
		self::assertTrue($set->setBucket('a')->setKey('a')->insert());
	}
	
	public function test_onInsert_ItemNotExists_ItemInserted()
	{
		$storage = new Storage();
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		$set->setBucket('a')->setKey('a')->insert();
		
		self::assertTrue($storage->hasKey('a', 'a'));
	}
	
	public function test_onUpdate_ItemExists_ReturnTrue()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		
		self::assertTrue($set->setBucket('a')->setKey('a')->update());
	}
	
	public function test_onUpdate_ItemExists_ItemUpdated()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		$set->setBucket('a')->setKey('a')->setTTL(12)->update();
		
		self::assertEquals(12, $storage->getItemIfExists('a', 'a')->TTL);
	}
	
	public function test_onUpdate_ItemNotExists_ReturnFalse()
	{
		$storage = new Storage();
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		
		self::assertFalse($set->setBucket('a')->setKey('a')->update());
	}
	
	public function test_onUpdate_ItemNotExists_ItemNotInserted()
	{
		$storage = new Storage();
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		$set->setBucket('a')->setKey('a')->update();
		
		self::assertFalse($storage->hasKey('a', 'a'));
	}
	
	public function test_onSave_ItemExists_ItemUpdated()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		$set->setBucket('a')->setKey('a')->setTTL(12)->save();
		
		self::assertEquals(12, $storage->getItemIfExists('a', 'a')->TTL);
	}
	
	public function test_onSave_ItemNotExists_ItemInserted()
	{
		$storage = new Storage();
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		$set->setBucket('a')->setKey('a')->save();
		
		self::assertTrue($storage->hasKey('a', 'a'));
	}
	
	public function test_onSave_ItemExists_ReturnTrue()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		
		self::assertTrue($set->setBucket('a')->setKey('a')->save());
	}
	
	public function test_onSave_ItemNotExists_ReturnTrue()
	{
		$storage = new Storage();
		$set = new Set($storage);
		$set->setSetEvents($this->createMock(ISetEvent::class));
		
		self::assertTrue($set->setBucket('a')->setKey('a')->save());
	}
	
	
	private function createItem(string $bucket, string $key, Storage $storage)
	{
		$data = new Data();
		$data->Bucket = $bucket;
		$data->Id = $key;
		$storage->setItem($data);
		
		return $storage;
	}
}