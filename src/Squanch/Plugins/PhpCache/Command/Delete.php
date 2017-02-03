<?php
namespace Squanch\Plugins\PhpCache\Command;


use Squanch\Enum\Callbacks;
use Squanch\Objects\CallbackData;
use Squanch\Base\Command\ICmdDelete;
use Squanch\AbstractCommand\AbstractDelete;

use Psr\Cache\CacheItemPoolInterface;
use Cache\Namespaced\NamespacedCachePool;


class Delete extends AbstractDelete implements ICmdDelete
{
	protected function getConnector(): CacheItemPoolInterface
	{
		return parent::getConnector();
	}
	
	
	public function execute(): bool
	{
		$fields = [];
		$callbackData = new CallbackData();
		
		$fields['Bucket'] = $this->getBucket();
		$callbackData->setBucket($this->getBucket());
		
		$bucket = new NamespacedCachePool($this->getConnector(), $this->getBucket());
		
		if ($this->getKey() && !$bucket->hasItem($this->getKey()))
		{
			$result = false;
		}		
		else if ($this->getKey())
		{
			$fields['Id'] = $this->getKey();
			$callbackData->setKey($this->getKey());
			
			$bucket->deleteItem($this->getKey());
			$result = !$bucket->hasItem($this->getKey());
		}
		else
		{
			$result = $bucket->clear();
		}
		
		if ($result)
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_DELETE, $callbackData);
		}
		else
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::FAIL_ON_DELETE, $callbackData);
		}
		
		$this->getCallbacksLoader()->executeCallback(Callbacks::ON_DELETE, $callbackData);
		
		$this->reset();
		
		return $result;
	}
}