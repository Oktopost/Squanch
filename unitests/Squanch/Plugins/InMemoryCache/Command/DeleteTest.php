<?php
namespace Squanch\Plugins\InMemoryCache\Command;


use Squanch\Base\Callbacks\Events\IDeleteEvent;
use Squanch\Objects\Data;
use Squanch\Plugins\InMemoryCache\Storage;


class DeleteTest extends \PHPUnit_Framework_TestCase
{
	public function test_onDeleteBucket_BucketNotFound_ReturnFalse()
	{
		$storage = new Storage();
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		
		self::assertFalse($delete->byBucket('a')->execute());
	}
	
	public function test_onDeleteBucket_BucketFound_ReturnTrue()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		
		self::assertTrue($delete->byBucket('a')->execute());
	}
	
	public function test_onDeleteBucket_BucketFound_BucketRemoved()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		$delete->byBucket('a')->execute();
		
		self::assertEmpty($storage->storage());
	}
	
	public function test_onDeleteBucket_DifferentBucketExists_DifferentBucketNotRemoved()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$this->createItem('b', 'b', $storage);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		$delete->byBucket('a')->execute();
		
		self::assertTrue($storage->hasBucket('b'));
	}
	
	public function test_onDeleteItem_BucketNotFound_ReturnFalse()
	{
		$storage = new Storage();
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		
		self::assertFalse($delete->byBucket('a')->byKey('a')->execute());
	}
	
	public function test_onDeleteItem_ItemNotFound_ReturnFalse()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'b', $storage);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		
		self::assertFalse($delete->byBucket('a')->byKey('a')->execute());
	}
	
	public function test_onDeleteItem_ItemFound_ReturnTrue()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		
		self::assertTrue($delete->byBucket('a')->byKey('a')->execute());
	}
	
	public function test_onDeleteItem_ItemFound_ItemRemoved()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		$delete->byBucket('a')->byKey('a')->execute();
		
		self::assertFalse($storage->hasKey('a', 'a'));
	}
	
	public function test_onDeleteItem_DifferentItemExists_DifferentItemNotRemoved()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		$this->createItem('a', 'b', $storage);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		$delete->byBucket('a')->byKey('a')->execute();
		
		self::assertTrue($storage->hasKey('a', 'b'));
	}
	
	public function test_onDeleteItem_DifferentBucketExists_DifferentItemNotRemoved()
	{
		$storage = new Storage();
		
		$this->createItem('a', 'a', $storage);
		$this->createItem('b', 'a', $storage);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		$delete->byBucket('a')->byKey('a')->execute();
		
		self::assertTrue($storage->hasKey('b', 'a'));
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