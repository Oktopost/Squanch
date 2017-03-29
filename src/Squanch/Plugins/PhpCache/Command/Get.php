<?php
namespace Squanch\Plugins\PhpCache\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\IGetCollection;
use Squanch\AbstractCommand\AbstractGet;

use Objection\Mapper;
use Psr\Cache\CacheItemPoolInterface;
use Cache\Namespaced\NamespacedCachePool;


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
			$json = $mapper->getObject($this->data);
			$bucket = new NamespacedCachePool($this->getConnector(), $this->getBucket());
			$bucket->getItem($this->getKey())->set($json)->expiresAt($this->newTTL);
			unset($this->newTTL);
		}
	}
	
	
	protected function getConnector(): CacheItemPoolInterface
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
		throw new \Exception('This method is not implemented for current decorator');
	}
	
	public function execute(): bool
	{
		$result = false;
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		
		$bucket = new NamespacedCachePool($this->getConnector(), $this->getBucket());
		
		$item = $bucket->getItem($this->getKey());
		
		if ($item->isHit())
		{
			$mapper = Mapper::createFor(Data::class);
			
			$this->data = $mapper->getObject($item->get());
			
			if ($this->data->TTL > 0)
			{
				$this->data->TTL = $this->data->EndDate->diff(new \DateTime())->format('%s');
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