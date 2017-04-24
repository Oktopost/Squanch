<?php
namespace dummyStorage\Command;


use Squanch\Base\Command\IGetCollection;
use Squanch\Collection\CollectionHandler;
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
	
	
	private function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	
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
	
	/**
	 * @return IGetCollection
	 */
	public function asCollection($limit = 999)
	{
		$result = false;
		$callbackData = (new CallbackData())->setBucket($this->getBucket());
		
		$this->executed = false;
		$bucket = $this->getBucket();
		
		$db = $this->getConnector()->getDb();
		$data = [];
		$now = new \DateTime();
		
		foreach ($db as $key=>$value)
		{
			if ($this->startsWith($key, $bucket))
			{
				$result = true;
				/** @var Data $item */
				$item = $db[$key];
				
				if($item->EndDate <= $now)
				{
					unset($db[$key]);
					continue;
				}
				
				if (isset($this->newTTL))
				{
					$item->setTTL($this->newTTL);
					unset($this->newTTL);
					$db[$key] = $item;
					$this->getConnector()->setDb($db);
				}
				
				$data[] = $db[$key];
			}
		}
		
		if ($result)
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::ON_GET, $callbackData);
			
			return new CollectionHandler($data);
		}
	
		$this->getCallbacksLoader()->executeCallback(Callbacks::MISS_ON_GET, $callbackData);
		
		return new CollectionHandler([]);
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
			$this->getCallbacksLoader()->executeCallback(Callbacks::MISS_ON_GET, $callbackData);
			
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