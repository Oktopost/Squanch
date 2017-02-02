<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Enum\Callbacks;
use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Base\Command\ICmdHas;
use Squanch\AbstractCommand\AbstractHas;

use Squid\MySql\Connectors\IMySqlObjectConnector;


class Has extends AbstractHas implements ICmdHas
{
	private $newTTL;
	
	private function updateTTLIfNeed()
	{
		if ($this->newTTL)
		{
			$get = new Get();
			$get->setup($this->getConnector(), $this->getCallbacksLoader());
			
			/** @var Data $object */
			$object = $get->byKey($this->getKey())->byBucket($this->getBucket())->asData();
			$object->setTTL($this->newTTL);
			$this->getConnector()->upsertByFields($object, ['Id', 'Bucket']);
			
			unset($this->newTTL);
		}
	}
	
	
	protected function getConnector(): IMySqlObjectConnector
	{
		return parent::getConnector();
	}
	
	
	public function execute(): bool
	{
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		
		/** @var Data $result */
		$result = $this->getConnector()->loadOneByFields(['Id' => $this->getKey(), 'Bucket' => $this->getBucket()]);
		
		if ($result)
		{
			$result = $result->EndDate > new \DateTime();
		}
		
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