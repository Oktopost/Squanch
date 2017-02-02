<?php
namespace dummyStorage\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
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
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		$result = false;
		
		$bucket = $this->getBucket();
		$key = $bucket.$this->getKey();
		
		$db = $this->connector->getDB();
		
		if (isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()) && $db[$key]->Bucket == $bucket)
		{
			$result = true;
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_HAS, $callbackData);
			
			/** @var Data $item */
			$item = $db[$key];
			
			if (isset($this->newTTL))
			{
				$item->setTTL($this->newTTL);
				$db[$key] = $item;
				$this->connector->setDb($db);
				unset($this->newTTL);
			}
			
			$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, $callbackData);
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_HAS, $callbackData);
			$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, $callbackData);
		}
		
		$this->reset();
		return $result;
	}
	
	public function resetTTL(int $ttl)
	{
		$this->newTTL = $ttl;
		return $this;
	}
}