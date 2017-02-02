<?php
namespace dummyStorage\Command;


use Squanch\Objects\CallbackData;
use Squanch\Objects\Data;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdGet;
use Squanch\AbstractCommand\AbstractGet;

use dummyStorage\DummyConnector;


class CmdGet extends AbstractGet implements ICmdGet
{
	/** @var Data */
	private $data;
	private $newTTL;
	private $executed = false;
	
	
	protected function executeIfNeed(): bool
	{
		if (!$this->executed)
		{
			return $this->execute();
		}
		
		return $this->executed;
	}
	
	protected function getConnector(): DummyConnector
	{
		return parent::getConnector();
	}
	
	protected function afterExecute()
	{
		$this->executed = false;
		$this->reset();
	}

	
	public function resetTTL(int $ttl)
	{
		$this->newTTL = $ttl;
		
		return $this;
	}
	
	public function execute(): bool
	{
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		
		$this->executed = false;
		$bucket = $this->getBucket();
		$key = $bucket.$this->getKey();
		
		$db = $this->getConnector()->getDb();
		
		if (isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()) && $db[$key]->Bucket == $bucket)
		{
			$callbackData->setData($db[$key]);
			$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_GET, $callbackData);
			
			/** @var Data $data */
			$data = $db[$key];
			
			if (isset($this->newTTL))
			{
				$data->setTTL($this->newTTL);
				unset($this->newTTL);
				$db[$key] = $data;
				$this->getConnector()->setDb($db);
			}
			
			$this->data = $data;
			$this->executed = true;
			$this->getCallbacksLoader()->executeCallback(Callbacks::ON_GET, $callbackData);
			
			return true;
		}
		else
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::FAIL_ON_GET, $callbackData);
			
			$this->getCallbacksLoader()->executeCallback(Callbacks::ON_GET, $callbackData);
			
			return false;
		}
	}
	
	/**
	 * @return Data|bool
	 */
	public function asData()
	{
		$this->executeIfNeed();
		
		return $this->data;
	}
}