<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Enum\Callbacks;
use Squanch\Enum\Events;
use Squanch\Objects\Data;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractSet;

use Squid\MySql\Connectors\IMySqlObjectConnector;

use Objection\Mapper;
use Objection\LiteObject;
use Objection\Mapper\Mappers\JsonFieldsMapper;


class Set extends AbstractSet implements ICmdSet
{
	/** @var IMySqlObjectConnector */
	private $connector;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	
	private $key;
	private $data;
	private $ttl;
	
	
	private function checkExists(): bool
	{
		$has = new Has($this->connector, $this->callbacksLoader);
		
		return $has->byKey($this->key)->execute();
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
	
	private function onFailCallback(Data $data)
	{
		$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_SET, ['key' => $this->key, 'data' => $data]);
	}
	
	private function onCompleteCallback(Data $data, bool $event)
	{
		$this->callbacksLoader->executeCallback(Callbacks::ON_SET, [
			'key' => $this->key, 'event' => $event ? Events::SUCCESS : Events::FAIL, 'data' => $data]);
	}
	
	private function onSuccessCallback($data)
	{
		$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_SET, ['key' => $this->key, 'data' => $data]);
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
		
		if ($this->data instanceof LiteObject)
		{
			$mapper = Mapper::createFor(
				get_class($this->data), 
				JsonFieldsMapper::instance($this->data->getPropertyNames())
			);
				
			$data->Value = $mapper->getJson($this->data);
		}
		else
		{
			$data->Value = json_encode($this->data);
		}
		
		$data->setTTL($this->ttl);
		
		if ($this->isInsertOnly() || $this->isUpdateOnly())
		{
			$exists = $this->checkExists();
			
			if ($this->isInsertOrUpdateOnly($exists))
			{
				$this->onFailCallback($data);
				$this->onCompleteCallback($data, false);
				$this->reset();
				
				return false;
			}
		}
		
		$result = $this->connector->upsertByFields($data, ['Id']);
		
		if ($result)
		{
			$this->onSuccessCallback($data);
			$this->onCompleteCallback($data, true);
		}
		else
		{
			$this->onFailCallback($data);
			$this->onCompleteCallback($data, false);
		}
		
		$this->reset();
		
		return true;
	}
}