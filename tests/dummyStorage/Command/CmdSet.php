<?php
namespace dummyStorage\Command;


use Objection\Mapper;
use Objection\LiteObject;

use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
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
		
		$db = $this->connector->getDb();
		$bucket = $this->getBucket();
		$key = $bucket . $this->getKey();
		$exists = isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()) && $db[$key]->Bucket == $bucket;
		
		$data = new Data();
		$data->Id = $key;
		$data->Bucket = $bucket;
		
		if ($this->getData() instanceof LiteObject)
		{
			$mapper = Mapper::createFor(
				get_class($this->getData())
			);
			
			$data->Value = $mapper->getJson($this->getData());
		}
		else if(is_scalar($this->getData()))
		{
			$data->Value = $this->getData();
		}
		else
		{
			$data->Value = json_encode($this->getData());
		}
		
		$data->setTTL($this->getTTL());
		
		$callbackData->setData($data);
		if (
			($exists && $this->isInsertOnly()) ||
			(!$exists && $this->isUpdateOnly())
		)
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_SET, $callbackData);
			
			$this->reset();
			
			return false;
		}
		
		$db[$key] = $data;
		$this->connector->setDb($db);
		
		$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_SET, $callbackData);
		
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_SET, $callbackData);
		
		$this->reset();
		
		return true;
	}
}