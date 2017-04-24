<?php
namespace Squanch\Plugins\Predis\Command;


use Objection\Mapper;
use Predis\Client;
use Squanch\AbstractCommand\AbstractGet;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\IGetCollection;
use Squanch\Collection\CollectionHandler;
use Squanch\Enum\Callbacks;
use Squanch\Objects\CallbackData;
use Squanch\Objects\Data;


class Get extends AbstractGet implements ICmdGet
{
	/** @var Data */
	private $data;
	
	private $newTTL;
	private $executed = false;
	
	
	private function updateTTLIfNeed(string $key = null)
	{
		if (isset($this->newTTL))
		{
			if (!$key)
				$key = $this->getKey();
			
			$this->data->setTTL($this->newTTL);
			$mapper = Mapper::createFor(Data::class);
			$json = $mapper->getJson($this->data);
			$this->getConnector()->hset($this->getBucket(), $key, $json);
			unset($this->newTTL);
		}
	}
	
	
	protected function getConnector(): Client
	{
		return parent::getConnector();
	}
		
	protected function afterExecute()
	{
		$this->executed = false;
		unset($this->newTTL);
		$this->reset();
	}
	
	protected function executeIfNeed(): bool
	{
		if (!$this->executed)
		{
			return $this->execute();
		}
		
		return $this->executed;
	}
	
	
	/**
	 * @return Data|bool
	 */
	public function asData()
	{
		$this->executeIfNeed();
		
		return $this->data;
	}
	
	/**
	 * @return IGetCollection
	 */
	public function asCollection($limit = 999)
	{
		$callbackData = (new CallbackData())->setBucket($this->getBucket());
		
		$mapper = Mapper::createFor(Data::class);
		
		/** @var Data[] $data */
		$data = $mapper->getObjects($this->getConnector()->hgetall($this->getBucket()));
		$now = new \DateTime();
		$toDel = [];
		$i = 0;
		
		foreach ($data as $key=>$item)
		{
			if ($i == $limit)
			{
				break;
			}
			
			if ($item->EndDate <= $now)
			{
				$toDel[] = $item->Id;
				unset($data[$key]);
				continue;
			}
			
			$this->data = $item;
			$this->updateTTLIfNeed($item->Id);
			$this->data = null;
			
			$i++;
		}
		
		if ($toDel)
		{
			$this->getConnector()->hdel($this->getBucket(), $toDel);
			
			if (!$this->getConnector()->hkeys($this->getBucket()))
			{
				$this->getConnector()->del([$this->getBucket()]);
			}
		}
	
		if ($data)
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_GET, $callbackData);
			$result = new CollectionHandler($data);
		}
		else
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::MISS_ON_GET, $callbackData);
			$result = new CollectionHandler([]);
		}
		
		$this->getCallbacksLoader()->executeCallback(Callbacks::ON_GET, $callbackData);
		
		return $result;
	}
	
	public function execute(): bool
	{
		$result = false;
		
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		
		$callbackData->setKey($this->getKey());
		$callbackData->setBucket($this->getBucket());
		
		$item = $this->getConnector()->hget($this->getBucket(), $this->getKey());
		
		if ($item)
		{
			$mapper = Mapper::createFor(Data::class);
			
			$this->data = $mapper->getObject($item);
			
			if ($this->data->EndDate < new \DateTime())
			{
				$this->getConnector()->hdel($this->getBucket(), [$this->getKey()]);
				$this->getCallbacksLoader()->executeCallback(Callbacks::MISS_ON_GET, $callbackData);
				$this->data = null;
				
				return false;
			}
			
			$result = true;
			$callbackData->setData($this->data);
			
			$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_GET, $callbackData);
			
			$this->updateTTLIfNeed();
		}
		else
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::MISS_ON_GET, $callbackData);
			$this->data = null;
		}
		
		$this->getCallbacksLoader()->executeCallback(Callbacks::ON_GET, $callbackData);
		
		
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