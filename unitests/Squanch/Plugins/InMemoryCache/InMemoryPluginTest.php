<?php
namespace Squanch\Plugins\InMemoryCache;


use Squanch\Base\Callbacks\ICacheEvents;
use Squanch\Plugins\InMemoryCache\Command\Delete;
use Squanch\Plugins\InMemoryCache\Command\Get;
use Squanch\Plugins\InMemoryCache\Command\Has;
use Squanch\Plugins\InMemoryCache\Command\Set;


class InMemoryPluginTest extends \PHPUnit_Framework_TestCase
{
	public function test_getCmdGet()
	{
		$plugin = new InMemoryPlugin();
		$plugin->setEventManager($this->createMock(ICacheEvents::class));
		
		self::assertInstanceOf(Get::class, $plugin->get());
	}
	
	public function test_getCmdHas()
	{
		$plugin = new InMemoryPlugin();
		$plugin->setEventManager($this->createMock(ICacheEvents::class));
		
		self::assertInstanceOf(Has::class, $plugin->has());
	}
	
	public function test_getCmdDelete()
	{
		$plugin = new InMemoryPlugin();
		$plugin->setEventManager($this->createMock(ICacheEvents::class));
		
		self::assertInstanceOf(Delete::class, $plugin->delete());
	}
	
	public function test_getCmdSet()
	{
		$plugin = new InMemoryPlugin();
		$plugin->setEventManager($this->createMock(ICacheEvents::class));
		
		self::assertInstanceOf(Set::class, $plugin->set());
	}
}