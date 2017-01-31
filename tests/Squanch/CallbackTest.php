<?php
namespace Squanch;

use Squanch\Base\ICallback;
use Squanch\Base\IPlugin;

use dummyStorage\Config;
use PHPUnit_Framework_TestCase;


require_once __DIR__.'/../dummyStorage/Config.php';


class CallbackTest extends PHPUnit_Framework_TestCase
{
	/** @var IPlugin */
	private $cache;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->cache = (new Config())->getPlugin();
	}
	
	
	public function test_callback_as_string_executes()
	{
		$this->cache->set('a', 'b')->onSuccess(myCallback::class)->execute();
		self::assertTrue(myCallback::getResult());
	}
	
	public function test_callback_as_class_executes()
	{
		$callback = new myCallback();
		$this->cache->set('a', 'b')->onSuccess($callback)->execute();
		
		self::assertTrue($callback::getResult());
	}
}


class myCallback implements ICallback
{
	private static $result = false;
	
	
	public function fire(array $data)
	{
		self::$result = true;
	}
	
	/**
	 * @return bool
	 */
	public static function getResult(): bool
	{
		return self::$result;
	}
}