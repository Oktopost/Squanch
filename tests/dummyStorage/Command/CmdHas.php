<?php
namespace dummyStorage\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdHas;
use Squanch\AbstractCommand\AbstractHas;

use dummyStorage\DummyConnector;


class CmdHas extends AbstractHas implements ICmdHas
{
	private $newTTL;
	
	
	protected function getConnector(): DummyConnector
	{
		return parent::getConnector();
	}
	
	
	public function execute(): bool
	{
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		$result = false;
		
		$bucket = $this->getBucket();
		$key = $bucket.$this->getKey();
		
		$db = $this->getConnector()->getDB();
		
		if (isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()) && $db[$key]->Bucket == $bucket)
		{
			$result = true;
			$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_HAS, $callbackData);
			
			/** @var Data $item */
			$item = $db[$key];
			
			if (isset($this->newTTL))
			{
				$item->setTTL($this->newTTL);
				$db[$key] = $item;
				$this->getConnector()->setDb($db);
				unset($this->newTTL);
			}
		}
		else
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::FAIL_ON_HAS, $callbackData);
		}
		
		$this->getCallbacksLoader()->executeCallback(Callbacks::ON_HAS, $callbackData);
		
		$this->reset();
		return $result;
	}
	
	public function resetTTL(int $ttl)
	{
		$this->newTTL = $ttl;
		return $this;
	}
}