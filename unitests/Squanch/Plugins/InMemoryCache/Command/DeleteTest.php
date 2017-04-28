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
		
		$data = new Data();
		$data->Bucket = 'a';
		$data->Id = 'a';
		$storage->setItem($data);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		
		self::assertTrue($delete->byBucket('a')->execute());
	}
	
	public function test_onDeleteBucket_BucketFound_BucketRemoved()
	{
		$storage = new Storage();
		
		$data = new Data();
		$data->Bucket = 'a';
		$data->Id = 'a';
		$storage->setItem($data);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		$delete->byBucket('a')->execute();
		
		self::assertEmpty($storage->storage());
	}
	
	public function test_onDeleteBucket_DifferentBucketExists_DifferentBucketNotRemoved()
	{
		$storage = new Storage();
		
		$data = new Data();
		$data->Bucket = 'a';
		$data->Id = 'a';
		$storage->setItem($data);
		
		$data = new Data();
		$data->Bucket = 'b';
		$data->Id = 'b';
		$storage->setItem($data);
		
		$delete = new Delete($storage);
		$delete->setDeleteEvents($this->createMock(IDeleteEvent::class));
		$delete->byBucket('a')->execute();
		
		self::assertTrue($storage->hasBucket('b'));
	}
}