<?php
namespace dummyStorage\Command;


use Objection\Mapper;
use Objection\LiteObject;

use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdSet;
use Squanch\AbstractCommand\AbstractSet;

use dummyStorage\DummyConnector;


class CmdSet extends AbstractSet implements ICmdSet
{
	protected function getConnector(): DummyConnector
	{
		return parent::getConnector();
	}

	
	public function execute(): bool
	{
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		
		$db = $this->getConnector()->getDb();
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
			$this->getCallbacksLoader()->executeCallback(Callbacks::FAIL_ON_SET, $callbackData);
			
			$this->reset();
			
			return false;
		}
		
		$db[$key] = $data;
		$this->getConnector()->setDb($db);
		
		$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_SET, $callbackData);
		
		
		$this->getCallbacksLoader()->executeCallback(Callbacks::ON_SET, $callbackData);
		
		$this->reset();
		
		return true;
	}
}