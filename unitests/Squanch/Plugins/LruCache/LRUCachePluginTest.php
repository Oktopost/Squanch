<?php
namespace Squanch\Plugins\LRUCache;


use Squanch\Base\Callbacks\ICacheEvents;
use Squanch\Plugins\LRUCache\Command\Delete;
use Squanch\Plugins\LRUCache\Command\Get;
use Squanch\Plugins\LRUCache\Command\Has;
use Squanch\Plugins\LRUCache\Command\Set;


class LRUCachePluginTest extends \PHPUnit_Framework_TestCase
{
	public function test_getCmdGet()
	{
		$plugin = new LRUCachePlugin(1000);
		$plugin->setEventManager($this->createMock(ICacheEvents::class));
		
		self::assertInstanceOf(Get::class, $plugin->get());
	}
	
	public function test_getCmdHas()
	{
		$plugin = new LRUCachePlugin(1000);
		$plugin->setEventManager($this->createMock(ICacheEvents::class));
		
		self::assertInstanceOf(Has::class, $plugin->has());
	}
	
	public function test_getCmdDelete()
	{
		$plugin = new LRUCachePlugin(1000);
		$plugin->setEventManager($this->createMock(ICacheEvents::class));
		
		self::assertInstanceOf(Delete::class, $plugin->delete());
	}
	
	public function test_getCmdSet()
	{
		$plugin = new LRUCachePlugin(1000);
		$plugin->setEventManager($this->createMock(ICacheEvents::class));
		
		self::assertInstanceOf(Set::class, $plugin->set());
	}
}