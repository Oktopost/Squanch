<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Enum\Callbacks;
use Squanch\Enum\Events;
use Squanch\Objects\Data;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractSet;

use Squid\MySql\Connectors\IMySqlObjectConnector;


class Set extends AbstractSet implements ICmdSet
{
	/** @var IMySqlObjectConnector */
	private $connector;
	
	/** @var ICallbacksLoader  */
	private $callbacksLoader;
	
	private $key;
	private $data;
	private $ttl;
	
	
	private function checkExists(): bool
	{
		return (bool)$this->connector->loadOneByField('Id', $this->key);
	}
	
	private function reset()
	{
		unset($this->data);
		unset($this->key);
		unset($this->ttl);
		$this->resetInsertAndUpdateOnly();
	}
	
	private function isInsertOrUpdateOnly($exists): bool
	{
		return ($exists && $this->isInsertOnly()) || (!$exists && $this->isUpdateOnly());
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
	
	
	public function setKey(string $key): ICmdSet
	{
		$this->key = $key;
		return $this;
	}
	
	public function setData($data): ICmdSet
	{
		$this->data = $data;
		return $this;
	}
	
	public function setTTL(int $ttl): ICmdSet
	{
		$this->ttl = $ttl;
		return $this;
	}
	
	public function setForever(): ICmdSet
	{
		return $this->setTTL(-1);
	}
	
	public function execute(): bool
	{
		$data = new Data();
		$data->Id = $this->key;
		$data->Value = json_encode($this->data);
		$data->setTTL($this->ttl);
		
		if ($this->isInsertOnly() || $this->isUpdateOnly())
		{
			$exists = $this->checkExists();
			
			if ($this->isInsertOrUpdateOnly($exists))
			{
				$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_SET, ['key' => $this->key, 'data' => $data]);
				$this->reset();
				return false;
			}
		}
		
		$this->connector->upsertByFields($data, ['Id']);
		$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_SET, ['key' => $this->key, 'data' => $data]);
		$this->callbacksLoader->executeCallback(Callbacks::ON_SET, [
			'key' => $this->key, 'event' => Events::SUCCESS, 'data' => $data]);
		
		$this->reset();
		
		return true;
	}
}