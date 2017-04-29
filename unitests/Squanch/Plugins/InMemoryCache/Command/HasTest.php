<?php
namespace Squanch\Plugins\InMemoryCache\Command;


use Squanch\Base\Callbacks\Events\IHasEvent;
use Squanch\Objects\Data;
use Squanch\Plugins\InMemoryCache\Storage;


class HasTest extends \PHPUnit_Framework_TestCase
{
	public function test_onCheck_KeyExists_ReturnTrue()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$has = new Has($storage);
		$has->setHasEvents($this->createMock(IHasEvent::class));
		
		self::assertTrue($has->byBucket('a')->byKey('a')->check());
	}
	
	public function test_onCheck_KeyNotExists_ReturnFalse()
	{
		$storage = new Storage();
		$has = new Has($storage);
		$has->setHasEvents($this->createMock(IHasEvent::class));
		
		self::assertFalse($has->byBucket('a')->byKey('a')->check());
	}
	
	public function test_onCheck_KeyExistsInDifferentBucket_ReturnFalse()
	{
		$storage = new Storage();
		
		$this->createItem('b', 'a', $storage);
		
		$has = new Has($storage);
		$has->setHasEvents($this->createMock(IHasEvent::class));
		
		self::assertFalse($has->byBucket('a')->byKey('a')->check());
	}
	
	public function test_onUpdateTTL_ItemFound_TTLUpdated()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$has = new Has($storage);
		$has->setHasEvents($this->createMock(IHasEvent::class));
		
		$has->byBucket('a')->byKey('a')->resetTTL(111)->check();
		
		self::assertEquals(111, $storage->getItemIfExists('a', 'a')->TTL);
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