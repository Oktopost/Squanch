<?php
namespace dummyStorage\Command;


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
		
		$data->Value = $this->getJsonData();
		
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