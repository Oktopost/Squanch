<?php
namespace Squanch\Decorators\Migration;


use Objection\LiteObject;

use Squanch\Enum\Callbacks;
use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;

use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Boot\ICallbacksLoader;


class MigrationGet implements ICmdGet
{
	private $newTTL;
	private $executed = false;
	
	private $main;
	private $fallback;
	
	/** @var ICmdGet */
	private $get;
	private $key;
	private $bucket;
	
	/** @var ICallbacksLoader */
	private $callbackLoader;
	
	
	private function executeIfNeed(): bool
	{
		if (!$this->executed)
		{
			return $this->execute();
		}
		
		return $this->executed;
	}
	
	private function executeMain()
	{
		$this->get = $this->main->get($this->key, $this->bucket);
		
		if ($this->newTTL)
		{
			$this->get->resetTTL($this->newTTL);
		}
		
		return $this->get->execute();
	}
	
	private function executeFallback()
	{
		$main = $this->main;
		$ttl = $this->newTTL;
		
		$this->get = $this->fallback->get($this->key, $this->bucket)->onSuccess(
			function(CallbackData $callbackData) use ($main, $ttl)
			{
				$main->set()
					->setKey($callbackData->Key)
					->setBucket($callbackData->Bucket)
					->setTTL(!is_null($ttl) ? $ttl : $callbackData->Data->TTL)
					->setData($callbackData->Data->Value)
					->execute();
			}
		);
		
		return $this->get->execute();
	}
	
	
	public function __construct(ICachePlugin $main, ICachePlugin $fallback)
	{
		$this->main = $main;
		$this->fallback = $fallback;
	}
	
	
	public function execute(): bool
	{
		$this->executed = false;
		$callbackData = new CallbackData();
		$callbackData->setKey($this->key)->setBucket($this->bucket);
		
		if (!$this->executeMain())
		{
			$result = $this->executeFallback();
		}
		else 
		{
			$result = true;
		}
		
		if ($result)
		{
			$callbackData->Data = $this->get->asData();
			$this->callbackLoader->executeCallback(Callbacks::SUCCESS_ON_GET, $callbackData);
		}
		else
		{
			$this->callbackLoader->executeCallback(Callbacks::FAIL_ON_GET, $callbackData);
		}
		
		$this->callbackLoader->executeCallback(Callbacks::ON_GET, $callbackData);
		
		$this->executed = true;
		
		return $result;
	}
	
	/**
	 * @return static
	 */
	public function resetTTL(int $ttl)
	{
		$this->newTTL = $ttl;
		return $this;
	}
	
	/**
	 * @return array|bool
	 */
	public function asArray()
	{
		$this->executeIfNeed();
		$result = isset($this->get) ? $this->get->asArray() : false;
		$this->executed = false;
		return $result;
	}
	
	/**
	 * @return \stdClass|bool
	 */
	public function asObject()
	{
		$this->executeIfNeed();
		
		$result = isset($this->get) ? $this->get->asObject() : false;
		$this->executed = false;
		return $result;
	}
	
	/**
	 * @return LiteObject|bool
	 */
	public function asLiteObject(string $liteObjectName)
	{
		$this->executeIfNeed();
		$result = isset($this->get) ? $this->get->asLiteObject($liteObjectName) : false;
		$this->executed = false;
		return $result;
	}
	
	/**
	 * @return string|bool
	 */
	public function asString()
	{
		$this->executeIfNeed();
		$result = isset($this->get) ? $this->get->asString() : false;
		$this->executed = false;
		return $result;
	}
	
	/**
	 * @return Data|bool
	 */
	public function asData()
	{
		$this->executeIfNeed();
		$result = isset($this->get) ? $this->get->asData() : false;
		$this->executed = false;
		return $result;
	}
	
	/**
	 * @return static
	 */
	public function byKey(string $key)
	{
		$this->key = $key;
		return $this;
	}
	
	/**
	 * @return static
	 */
	public function byBucket(string $bucket)
	{
		$this->bucket = $bucket;
		return $this;
	}
	
	public function setup($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->callbackLoader = $callbacksLoader;
		return $this;
	}
	
	public function onSuccess($onSuccess)
	{
		$this->callbackLoader->addCallback(Callbacks::SUCCESS_ON_GET, $onSuccess);
		return $this;
	}
	
	public function onFail($onFail)
	{
		$this->callbackLoader->addCallback(Callbacks::FAIL_ON_GET, $onFail);
		return $this;
	}
	
	public function onComplete($onComplete)
	{
		$this->callbackLoader->addCallback(Callbacks::ON_GET, $onComplete);
		return $this;
	}
}