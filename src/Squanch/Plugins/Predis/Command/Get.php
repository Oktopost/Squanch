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
	
	
	private function updateTTLIfNeed()
	{
		if (isset($this->newTTL))
		{
			$this->data->setTTL($this->newTTL);
			$mapper = Mapper::createFor(Data::class);
			$json = $mapper->getJson($this->data);
			$this->getConnector()->hset($this->getBucket(), $this->getKey(), $json);
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
		$mapper = Mapper::createFor(Data::class);
		$data = $mapper->getObjects($this->getConnector()->hgetall($this->getBucket()));
		
		if ($this->newTTL)
		{
			foreach ($data as $item)
			{
				$this->data = $item;
				$this->updateTTLIfNeed();
				$this->data = null;
			}
		}
		
		return new CollectionHandler($data);
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
				$this->getCallbacksLoader()->executeCallback(Callbacks::FAIL_ON_GET, $callbackData);
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
			$this->getCallbacksLoader()->executeCallback(Callbacks::FAIL_ON_GET, $callbackData);
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