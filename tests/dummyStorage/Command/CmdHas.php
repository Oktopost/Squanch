<?php
namespace dummyStorage\Command;


use Squanch\Enum\Events;
use Squanch\Objects\Data;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\IByKey;
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
	private $key;
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
	
	
	public function byKey(string $key): IByKey
	{
		$this->key = $key;
		return $this;
	}
	
	public function execute(): bool
	{
		$key = $this->key;
		unset($this->key);
		
		$db = $this->connector->getDB();
		
		if (isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()))
		{
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_HAS, ['key' => $key]);
			
			/** @var Data $item */
			$item = $db[$key];
			
			if (isset($this->newTTL))
			{
				$item->setTTL($this->newTTL);
				$db[$key] = $item;
				$this->connector->setDb($db);
				unset($this->newTTL);
			}
			
			$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, ['key' => $key, 'event' => Events::SUCCESS]);
			
			return true;
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_HAS, ['key' => $key]);
			$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, ['key' => $key, 'event' => Events::FAIL]);
			
			return false;
		}
	}
	
	public function resetTTL(int $ttl)
	{
		$this->newTTL = $ttl;
		return $this;
	}
}