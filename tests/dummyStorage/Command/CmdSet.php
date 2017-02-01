<?php
namespace dummyStorage\Command;


use Objection\Mapper;
use Objection\LiteObject;

use Squanch\Objects\Data;
use Squanch\Enum\Events;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractSet;

use dummyStorage\DummyConnector;


class CmdSet extends AbstractSet implements ICmdSet
{
	/** @var  DummyConnector */
	private $connector;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	private $data;
	private $key;
	private $ttl;
	private $insertOnly;
	private $updateOnly;
	
	
	private function reset()
	{
		unset($this->data);
		unset($this->key);
		unset($this->ttl);
		unset($this->insertOnly);
		unset($this->updateOnly);
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
		$this->ttl = -1;
		
		return $this;
	}
	
	public function execute(): bool
	{
		$db = $this->connector->getDb();
		$key = $this->key;
		$exists = isset($db[$key]) && $db[$key]->EndDate > (new \DateTime());
		
		$data = new Data();
		$data->Id = $this->key;
		
		if ($this->data instanceof LiteObject)
		{
			$mapper = Mapper::createFor(
				get_class($this->data)
			);
			
			$data->Value = $mapper->getJson($this->data);
		}
		else if(is_scalar($this->data))
		{
			$data->Value = $this->data;
		}
		else
		{
			$data->Value = json_encode($this->data);
		}
		
		$data->setTTL($this->ttl);
		
		if (
			($exists && $this->isInsertOnly()) ||
			(!$exists && $this->isUpdateOnly())
		)
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_SET, ['key' => $this->key, 'data' => $data]);
			
			$this->reset();
			
			return false;
		}
		
		$db[$this->key] = $data;
		$this->connector->setDb($db);
		
		$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_SET, ['key' => $this->key, 'data' => $data]);
		
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_SET, [
			'key' => $this->key, 'event' => Events::SUCCESS, 'data' => $data]);
		
		$this->reset();
		
		return true;
	}
}