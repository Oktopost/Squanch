<?php
namespace dummyStorage\Command;


use Squanch\Enum\Events;
use Squanch\Objects\Data;
use Squanch\Enum\Callbacks;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Base\Command\IByKey;
use Squanch\Base\Command\ICmdGet;
use Squanch\AbstractCommand\AbstractGet;

use dummyStorage\DummyConnector;


class CmdGet extends AbstractGet implements ICmdGet
{
	/** @var DummyConnector */
	private $connector;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	
	/** @var Data */
	private $data;
	private $key;
	private $newTTL;
	private $executed = false;
	
	
	protected function executeIfNeed()
	{
		if ($this->executed == false)
		{
			$this->execute();
		}
	}
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	protected function afterExecute()
	{
		$this->executed = false;
	}

	
	public function __construct($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
	}
	
	
	public function byKey(string $key): IByKey
	{
		$this->key = $key;
		
		return $this;
	}
	
	public function resetTTL(int $ttl)
	{
		$this->newTTL = $ttl;
		
		return $this;
	}
	
	public function execute(): bool
	{
		$this->executed = false;
		$key = $this->key;
		unset($this->key);
		
		$db = $this->connector->getDb();
		
		if (isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()))
		{
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_GET, ['key' => $key, 'data' => $db[$key]]);
			
			/** @var Data $data */
			$data = $db[$key];
			
			if (isset($this->newTTL))
			{
				$data->setTTL($this->newTTL);
				unset($this->newTTL);
				$db[$key] = $data;
				$this->connector->setDb($db);
			}
			
			$this->data = $data;
			$this->executed = true;
			$this->callbacksLoader->executeCallback(Callbacks::ON_GET,
				['key' => $key, 'event' => Events::SUCCESS, 'data' => $db[$key]]);
			
			return true;
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_GET, [
				'key' => $key
			]);
			
			$this->callbacksLoader->executeCallback(Callbacks::ON_GET,
				['key' => $key, 'event' => Events::FAIL]);
			
			return false;
		}
	}
	
	/**
	 * @return Data|bool
	 */
	public function asData()
	{
		$this->executeIfNeed();
		
		return $this->data;
	}
}