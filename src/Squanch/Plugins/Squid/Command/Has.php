<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Enum\Callbacks;
use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractHas;

use Squid\MySql\Connectors\IMySqlObjectConnector;


class Has extends AbstractHas implements ICmdHas
{
	/** @var IMySqlObjectConnector */
	private $connector;
	
	/** @var ICallbacksLoader  */
	private $callbacksLoader;
	private $newTTL;
	
	private function updateTTLIfNeed()
	{
		if ($this->newTTL)
		{
			$get = new Get($this->connector, $this->getCallbacksLoader());
			
			/** @var Data $object */
			$object = $get->byKey($this->getKey())->byBucket($this->getBucket())->asData();
			$object->setTTL($this->newTTL);
			$this->connector->upsertByFields($object, ['Id', 'Bucket']);
			
			unset($this->newTTL);
		}
	}
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	
	public function __construct($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
	}
	
	
	public function execute(): bool
	{
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		
		/** @var Data $result */
		$result = $this->connector->loadOneByFields(['Id' => $this->getKey(), 'Bucket' => $this->getBucket()]);
		
		if ($result)
		{
			$result = $result->EndDate > new \DateTime();
		}
		
		if ($result)
		{
			$this->updateTTLIfNeed();
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_HAS, $callbackData);
			$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, $callbackData);
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_HAS, $callbackData);
		}
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_HAS, $callbackData);
		
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