<?php
namespace Squanch\Callbacks;


use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Enum\Callbacks;
use Squanch\Base\Callbacks\IGetCallback;
use Squanch\Base\Callbacks\ISetCallback;
use Squanch\Base\Callbacks\IHasCallback;
use Squanch\Base\Callbacks\IDeleteCallback;
use Squanch\Objects\CallbackData;


class CallbacksHandler implements 
	IDeleteCallback, 
	IHasCallback, 
	IGetCallback,
	ISetCallback
{
	/** @var ICallbacksLoader */
	private $loader;
	
	
	private function executeCallbacks(array $keys, CallbackData $data)
	{
		foreach ($keys as $key)
		{
			$this->loader->executeCallback($key, $data);
		}
	}
	
	
	public function __construct(ICallbacksLoader $callbackLoader)
	{
		$this->loader = $callbackLoader;
	}


	public function onDeleteRequest(bool $result, CallbackData $data)
	{
		$this->executeCallbacks([
				$result ? Callbacks::SUCCESS_ON_DELETE : Callbacks::FAIL_ON_DELETE,
				Callbacks::ON_DELETE
			],
			$data
		);
	}

	public function onHasRequest(bool $result, CallbackData $data)
	{
		$this->executeCallbacks([
				$result ? Callbacks::SUCCESS_ON_HAS : Callbacks::FAIL_ON_HAS,
				Callbacks::ON_HAS
			],
			$data
		);
	}

	public function onGetRequest(bool $result, CallbackData $data)
	{
		$this->executeCallbacks([
				$result ? Callbacks::SUCCESS_ON_GET : Callbacks::MISS_ON_GET,
				Callbacks::ON_GET
			],
			$data
		);
	}

	public function onSetRequest(bool $result, CallbackData $data)
	{
		$this->executeCallbacks([
				$result ? Callbacks::SUCCESS_ON_SET : Callbacks::FAIL_ON_SET,
				Callbacks::ON_SET
			],
			$data
		);
	}
}