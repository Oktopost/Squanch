<?php
namespace Squanch;

use Squanch\Base\ICallback;
use Squanch\Base\ICachePlugin;

use dummyStorage\Config;
use Squanch\Enum\Callbacks;
use Squanch\Objects\CallbackData;


require_once __DIR__.'/../dummyStorage/Config.php';


class CallbackTest extends \PHPUnit_Framework_TestCase
{
	/** @var ICachePlugin */
	private $cache;
	
	
	protected function setUp()
	{
		parent::setUp();
		$this->cache = (new Config())->getPlugin();
	}
	
	public function test_global_callback_executes()
	{
		$increment = 0;
		
		$loader = $this->cache->getCallbacksLoader();
		$loader->addGlobalCallback(Callbacks::SUCCESS_ON_GET, function() use(&$increment){
			$increment++;
		});
		
		$id = uniqid();
		$this->cache->set($id, 'test')->execute();
		
		$this->cache->get($id)->execute();
		$this->cache->get($id)->execute();
		$this->cache->get($id)->execute();
		
		self::assertEquals(3, $increment);
		
		$this->cache->delete($id)->execute();
	}
	
	public function test_runtime_callback_prevents_global_callback()
	{
		$increment = 1;
		
		$loader = $this->cache->getCallbacksLoader();
		$loader->addGlobalCallback(Callbacks::SUCCESS_ON_GET, function() use(&$increment){
			$increment++;
		});
		
		$id = uniqid();
		$this->cache->set($id, 'test')->execute();
		
		$this->cache->get($id)->onSuccess(function() use(&$increment){
			$increment--;
			return false;
		})->execute();
		
		self::assertEquals(0, $increment);
		
		$this->cache->delete($id);
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
	
	public function test_second_query_not_affected_by_callback_on_first_query()
	{
		$increment = 0;
		
		$this->cache->get(uniqid())->onComplete(function() use(&$increment){
			$increment++;
		})->execute();
		
		$this->cache->get(uniqid())->execute();
	
		self::assertEquals(1, $increment);
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