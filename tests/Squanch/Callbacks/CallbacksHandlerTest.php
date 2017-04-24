<?php
namespace Squanch\Callbacks;


use Squanch\Enum\Callbacks;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Objects\CallbackData;


class CallbacksHandlerTest extends \PHPUnit_Framework_TestCase
{
	private function mockICallbacksLoaderExpectKeys(array $keys, CallbackData $data): ICallbacksLoader
	{
		$obj = $this->createMock(ICallbacksLoader::class);
		
		for ($i = 0; $i < count($keys); $i++)
		{
			$obj->expects($this->at($i))
				->method('executeCallback')
				->with($keys[$i], $data)
				->willReturnSelf();
		}

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $obj;
	}
	
	private function doMethodTest(array $flags, bool $result, string $methodName)
	{
		$dataObject = new CallbackData();
		
		$obj = $this->mockICallbacksLoaderExpectKeys($flags, $dataObject);
		
		$testSubject = new CallbacksHandler($obj);
		$testSubject->$methodName($result, $dataObject);
	}
	
	
	public function test_onDeleteRequest_onFail()
	{
		$this->doMethodTest([
				Callbacks::FAIL_ON_DELETE,
				Callbacks::ON_DELETE
			],
			false,
			'onDeleteRequest');
	}
	
	public function test_onDeleteRequest_onSuccess()
	{
		$this->doMethodTest([
				Callbacks::SUCCESS_ON_DELETE,
				Callbacks::ON_DELETE
			],
			true,
			'onDeleteRequest');
	}
	
	
	public function test_onHasRequest_onFail()
	{
		$this->doMethodTest([
				Callbacks::FAIL_ON_HAS,
				Callbacks::ON_HAS
			],
			false,
			'onHasRequest');
	}
	
	public function test_onHasRequest_onSuccess()
	{
		$this->doMethodTest([
				Callbacks::SUCCESS_ON_HAS,
				Callbacks::ON_HAS
			],
			true,
			'onHasRequest');
	}
	
	
	public function test_onGetRequest_onFail()
	{
		$this->doMethodTest([
				Callbacks::MISS_ON_GET,
				Callbacks::ON_GET
			],
			false,
			'onGetRequest');
	}
	
	public function test_onGetRequest_onSuccess()
	{
		$this->doMethodTest([
				Callbacks::SUCCESS_ON_GET,
				Callbacks::ON_GET
			],
			true,
			'onGetRequest');
	}
	
	
	public function test_onSetRequest_onFail()
	{
		$this->doMethodTest([
				Callbacks::FAIL_ON_SET,
				Callbacks::ON_SET
			],
			false,
			'onSetRequest');
	}
	
	public function test_onSetRequest_onSuccess()
	{
		$this->doMethodTest([
				Callbacks::SUCCESS_ON_SET,
				Callbacks::ON_SET
			],
			true,
			'onSetRequest');
	}
}