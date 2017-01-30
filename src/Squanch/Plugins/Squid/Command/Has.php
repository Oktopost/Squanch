<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Enum\Events;
use Squanch\Enum\Callbacks;
use Squanch\Objects\Data;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractHas;

use Squid\MySql\Connectors\IMySqlObjectConnector;


class Has extends AbstractHas implements ICmdHas
{
	/** @var IMySqlObjectConnector */
	private $connector;
	
	/** @var ICallbacksLoader  */
	private $callbacksLoader;
	private $key;
	private $newTTL;
	
	private function updateTTLIfNeed()
	{
		if ($this->newTTL)
		{
			/** @var Data $object */
			$object = $this->connector->loadOneByField('Id', $this->key);
			$object->setTTL($this->newTTL);
			$this->connector->upsertByFields($object, ['Id']);
			
			unset($this->newTTL);
		}
	}
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	
	public function __construct($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
	}
	
	
	/**
	 * @return static
	 */
	public function byKey(string $key)
	{
		$this->key = $key;
		return $this;
	}
	
	public function execute(): bool
	{
		/** @var Data $result */
		$result = $this->connector->loadOneByField('Id', $this->key);
		
		if ($result)
		{
			$result = $result->EndDate > new \DateTime();
		}
		
		if ($result)
		{
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_HAS, ['key' => $this->key]);
			$this->updateTTLIfNeed();
			$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, ['key' => $this->key, 'event' => Events::SUCCESS]);
			
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_HAS, ['key' => $this->key]);
		}
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, ['key' => $this->key, 'event' => Events::FAIL]);
		
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
}