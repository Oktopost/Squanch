<?php
namespace dummyStorage\Command;


use Squanch\Enum\Events;
use Squanch\Objects\Data;
use Squanch\Enum\Callbacks;
use Squanch\Base\Boot\ICallbacksLoader;
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
	private $newTTL;
	private $executed = false;
	
	
	protected function executeIfNeed(): bool
	{
		if (!$this->executed)
		{
			return $this->execute();
		}
		
		return $this->executed;
	}
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	protected function afterExecute()
	{
		$this->executed = false;
		$this->reset();
	}

	
	public function __construct($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
	}
	
	
	public function resetTTL(int $ttl)
	{
		$this->newTTL = $ttl;
		
		return $this;
	}
	
	public function execute(): bool
	{
		$this->executed = false;
		$bucket = $this->getBucket();
		$key = $bucket.$this->getKey();
		
		$db = $this->connector->getDb();
		
		if (isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()) && $db[$key]->Bucket == $bucket)
		{
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_GET, [
				'key' => $key, 'bucket' => $bucket, 'data' => $db[$key]]
			);
			
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
				['key' => $key, 'bucket' => $bucket, 'event' => Events::SUCCESS, 'data' => $db[$key]]);
			
			return true;
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_GET, [
				'key' => $key,
				'bucket' => $bucket
			]);
			
			$this->callbacksLoader->executeCallback(Callbacks::ON_GET,
				['key' => $key, 'bucket' => $bucket, 'event' => Events::FAIL]);
			
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