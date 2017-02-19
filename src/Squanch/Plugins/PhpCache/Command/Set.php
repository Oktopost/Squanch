<?php
namespace Squanch\Plugins\PhpCache\Command;


use Squanch\Enum\Callbacks;
use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Base\Command\ICmdSet;
use Squanch\AbstractCommand\AbstractSet;

use Psr\Cache\CacheItemPoolInterface;
use Cache\Namespaced\NamespacedCachePool;

use Objection\Mapper;


class Set extends AbstractSet implements ICmdSet
{
	private function checkExists(): bool
	{
		$has = new Has();
		$has->setup($this->getConnector(), $this->getCallbacksLoader());
		
		return $has->byKey($this->getKey())->byBucket($this->getBucket())->execute();
	}
	
	private function isInsertOrUpdateOnly($exists): bool
	{
		return ($exists && $this->isInsertOnly()) || (!$exists && $this->isUpdateOnly());
	}
	
	private function getCallbackData(Data $data): CallbackData
	{
		return (new CallbackData())
			->setKey($this->getKey())
			->setBucket($this->getBucket())
			->setData($data);
	}
	
	private function onFailCallback(Data $data)
	{
		$this->getCallbacksLoader()->executeCallback(Callbacks::FAIL_ON_SET, $this->getCallbackData($data));
	}
	
	private function onCompleteCallback(Data $data)
	{
		$this->getCallbacksLoader()->executeCallback(Callbacks::ON_SET, $this->getCallbackData($data));
	}
	
	private function onSuccessCallback($data)
	{
		$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_SET, $this->getCallbackData($data));
	}
	
	
	protected function getConnector(): CacheItemPoolInterface
	{
		return parent::getConnector();
	}

	
	public function execute(): bool
	{
		$data = new Data();
		$data->Id = $this->getKey();
		$data->Bucket = $this->getBucket();
		
		$data->Value = $this->getJsonData();
		
		$data->setTTL($this->getTTL());
		
		if ($this->isInsertOnly() || $this->isUpdateOnly())
		{
			$exists = $this->checkExists();
			
			if ($this->isInsertOrUpdateOnly($exists))
			{
				$this->onFailCallback($data);
				$this->onCompleteCallback($data);
				$this->reset();
				
				return false;
			}
		}
		
		$bucket = new NamespacedCachePool($this->getConnector(), $this->getBucket());
		
		
		$mapper = Mapper::createFor(Data::class);
		
		$item = $bucket->getItem($this->getKey())
			->expiresAt($data->EndDate)
			->set($mapper->getJson($data));
		
		$result = $bucket->save($item);
		
		if ($result)
		{
			$this->onSuccessCallback($data);
		}
		else
		{
			$this->onFailCallback($data);
		}
		
		$this->onCompleteCallback($data);
		
		$this->reset();
		
		return true;
	}
}