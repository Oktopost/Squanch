<?php
namespace dummyStorage\Command;


use Squanch\Enum\Events;
use Squanch\Objects\Data;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractHas;

use dummyStorage\DummyConnector;


class CmdHas extends AbstractHas implements ICmdHas
{
	/** @var DummyConnector */
	private $connector;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	private $newTTL;
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	
	public function __construct($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
	}
	
	
	public function execute(): bool
	{
		$bucket = $this->getBucket();
		$key = $bucket.$this->getKey();
		
		$db = $this->connector->getDB();
		
		if (isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()) && $db[$key]->Bucket == $bucket)
		{
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_HAS, ['key' => $key, 'bucket' => $bucket]);
			
			/** @var Data $item */
			$item = $db[$key];
			
			if (isset($this->newTTL))
			{
				$item->setTTL($this->newTTL);
				$db[$key] = $item;
				$this->connector->setDb($db);
				unset($this->newTTL);
			}
			
			$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, [
				'key' => $key, 'bucket' => $bucket, 'event' => Events::SUCCESS]
			);
			
			$this->reset();
			return true;
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_HAS, ['key' => $key, 'bucket' => $bucket]);
			$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, [
				'key' => $key, 'bucket' => $bucket, 'event' => Events::FAIL]
			);
			$this->reset();
			return false;
		}
	}
	
	public function resetTTL(int $ttl)
	{
		$this->newTTL = $ttl;
		return $this;
	}
}