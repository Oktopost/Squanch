<?php
namespace Squanch\Plugins\InMemoryCache\Command;


use Squanch\Base\Callbacks\Events\IGetEvent;
use Squanch\Objects\Data;
use Squanch\Plugins\InMemoryCache\Storage;


class GetTest extends \PHPUnit_Framework_TestCase
{
	public function test_onGet_ItemNotExists_ReturnNull()
	{
		$storage = new Storage();
		$get = new Get($storage);
		$get->setGetEvents($this->createMock(IGetEvent::class));
		
		self::assertNull($get->byBucket('a')->byKey('a')->asData());
	}
	
	public function test_onGet_ItemExists_ReturnData()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$get = new Get($storage);
		$get->setGetEvents($this->createMock(IGetEvent::class));
		
		self::assertInstanceOf(Data::class, $get->byBucket('a')->byKey('a')->asData());
	}
	
	public function test_onGet_ItemExistsInDifferentBucket_ReturnNull()
	{
		$storage = new Storage();
		
		$this->createItem('b', 'a', $storage);
		
		$get = new Get($storage);
		$get->setGetEvents($this->createMock(IGetEvent::class));
		
		self::assertNull($get->byBucket('a')->byKey('a')->asData());
	}
	
	public function test_onUpdateTTL_ItemFound_TTLUpdated()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$get = new Get($storage);
		$get->setGetEvents($this->createMock(IGetEvent::class));
		
		$data = $get->byBucket('a')->byKey('a')->resetTTL(111)->asData();
		
		self::assertEquals(111, $data->TTL);
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