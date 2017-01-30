<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Objects\Data;
use Squanch\Enum\Events;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractGet;

use Squid\MySql\Connectors\IMySqlObjectConnector;


class Get extends AbstractGet implements ICmdGet
{
	/** @var IMySqlObjectConnector */
	private $connector;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	
	/** @var Data */
	private $data;
	
	private $key;
	private $newTTL;
	private $executed = false;
	
	
	private function updateTTLIfNeed()
	{
		if (isset($this->newTTL))
		{
			$this->data->setTTL($this->newTTL);
			$this->connector->updateObjectByFields($this->data, ['Id']);
			unset($this->newTTL);
		}
	}
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	protected function afterExecute()
	{
		$this->executed = false;
		unset($this->newTTL);
		unset($this->key);
	}
	
	protected function executeIfNeed()
	{
		if ($this->executed == false)
		{
			$this->execute();
		}
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
	
	/**
	 * @return Data|bool
	 */
	public function asData()
	{
		$this->executeIfNeed();
		
		return $this->data;
	}
	
	public function execute(): bool
	{
		$result = false;
		
		$this->data = $this->connector->loadOneByField('Id', $this->key);
		
		if ($this->data && $this->data->EndDate > new \DateTime())
		{
			$result = true;
			
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_GET,
				['key' => $this->key, 'data' => $this->data]);
			
			$this->updateTTLIfNeed();
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_GET, ['key' => $this->key]);
			$this->data = null;
		}
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_GET,
			['key' => $this->key, 'event' => $result ? Events::SUCCESS : Events::FAIL]
		);
		
		
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