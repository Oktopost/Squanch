<?php
namespace Squanch\Plugins\LRUCache\Command;


use Squanch\Objects\Data;
use Squanch\Base\Callbacks\Events\IGetEvent;
use Squanch\Plugins\LRUCache\LRUAdapter;
use Squanch\Plugins\LRUCache\LRUCachePlugin;


class GetTest extends \PHPUnit_Framework_TestCase
{
	public function test_onGet_ItemNotExists_ReturnNull()
	{
		$lruCache = new LRUCachePlugin(10);

		self::assertNull($lruCache->get()->byBucket('a')->byKey('a')->asData());
	}
	
	public function test_onGet_ItemExists_ReturnData()
	{
		$lruCache = new LRUCachePlugin(10);
		$lruCache->set('a', null, 'a')->save();

		self::assertInstanceOf(Data::class, $lruCache->get()->byBucket('a')->byKey('a')->asData());
	}
	
	public function test_onGet_ItemExistsInDifferentBucket_ReturnNull()
	{
		$lruCache = new LRUCachePlugin(10);
		$lruCache->set('a', null, 'b')->save();
		
		self::assertNull($lruCache->get()->byBucket('a')->byKey('a')->asData());
	}
	
	public function test_onGet_ItemExistsAndOutdated_ReturnNull()
	{
		$lruAdapter = new LRUAdapter(10);
		
		$data = new Data();
		$data->Bucket = 'a';
		$data->Id = 'a';
		
		$time = time() - 24 * 60 * 60;
		$data->EndDate = new \DateTime('@' . $time);
		
		$lruAdapter->setItem($data);
		
		$get = new Get($lruAdapter);
		$get->setGetEvents($this->createMock(IGetEvent::class));

		self::assertNull($get->byBucket('a')->byKey('a')->asData());
	}
	
	public function test_onUpdateTTL_ItemFound_TTLUpdated()
	{
		$lruCache = new LRUCachePlugin(10);
		
		$data = new Data();
		$data->Bucket = 'a';
		$data->Id = 'a';
		
		$lruCache->set('a', $data, 'a')->save();
		
		$data = $lruCache->get()->byBucket('a')->byKey('a')->resetTTL(111)->asData();
		
		self::assertEquals(111, $data->TTL);
	}
}