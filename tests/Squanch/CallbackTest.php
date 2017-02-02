<?php
namespace Squanch;

use Squanch\Base\ICallback;
use Squanch\Base\ICachePlugin;

use dummyStorage\Config;
use PHPUnit_Framework_TestCase;
use Squanch\Objects\CallbackData;


require_once __DIR__.'/../dummyStorage/Config.php';


class CallbackTest extends PHPUnit_Framework_TestCase
{
	/** @var ICachePlugin */
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
	
	
	public function fire(CallbackData $data)
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