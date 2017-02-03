<?php
namespace Squanch\Plugins\PhpCache\Command;


use Squanch\Enum\Callbacks;
use Squanch\Objects\CallbackData;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractHas;

use Psr\Cache\CacheItemPoolInterface;
use Cache\Namespaced\NamespacedCachePool;


class Has extends AbstractHas implements ICmdHas
{
	private $newTTL;
	
	
	private function updateTTLIfNeed()
	{
		if ($this->newTTL)
		{
			$get = new Get();
			/** @var ICallbacksLoader $emptyCallbacksLoader */
			$emptyCallbacksLoader = \Squanch::skeleton(ICallbacksLoader::class);
			$get->setup($this->getConnector(), $emptyCallbacksLoader);
			$get->byKey($this->getKey())->byBucket($this->getBucket())->resetTTL($this->newTTL)->execute();
			unset($this->newTTL);
		}
	}
	
	
	protected function getConnector(): CacheItemPoolInterface
	{
		return parent::getConnector();
	}
	
	
	public function execute(): bool
	{
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		
		$bucket = new NamespacedCachePool($this->getConnector(), $this->getBucket());
		$result = $bucket->hasItem($this->getKey());
		
		if ($result)
		{
			$this->updateTTLIfNeed();
			$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_HAS, $callbackData);
			$this->getCallbacksLoader()->executeCallback(Callbacks::ON_HAS, $callbackData);
		}
		else
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::FAIL_ON_HAS, $callbackData);
		}
		
		$this->getCallbacksLoader()->executeCallback(Callbacks::ON_HAS, $callbackData);
		
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