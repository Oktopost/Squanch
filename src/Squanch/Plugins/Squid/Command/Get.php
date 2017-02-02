<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdGet;
use Squanch\AbstractCommand\AbstractGet;

use Squid\MySql\Connectors\IMySqlObjectConnector;


class Get extends AbstractGet implements ICmdGet
{
	/** @var Data */
	private $data;
	
	private $newTTL;
	private $executed = false;
	
	
	protected function getConnector(): IMySqlObjectConnector
	{
		return parent::getConnector();
	}
	
	private function updateTTLIfNeed()
	{
		if (isset($this->newTTL))
		{
			$this->data->setTTL($this->newTTL);
			$this->getConnector()->updateObjectByFields($this->data, ['Id']);
			unset($this->newTTL);
		}
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
	
	public function execute(): bool
	{
		$result = false;
		$callbackData = (new CallbackData())->setKey($this->getKey())->setBucket($this->getBucket());
		
		$this->data = $this->getConnector()->loadOneByFields(['Id' => $this->getKey(), 'Bucket' => $this->getBucket()]);
		
		if ($this->data && $this->data->EndDate > new \DateTime())
		{
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