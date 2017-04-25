<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\IGetCollection;
use Squanch\Collection\CollectionHandler;
use Squanch\AbstractCommand\AbstractGet;
use Squanch\Exceptions\SquanchException;

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
	
	/**
	 * @return IGetCollection
	 */
	public function asCollection(int $limit = 1000)
	{
		$callbackData = (new CallbackData());
		
		$fields = [];
		
		if ($this->getKey())
		{
			$fields['Id'] = $this->getKey();
			$callbackData->setKey($this->getKey());
		}
		
		if ($this->getBucket())
		{
			$fields['Bucket'] = $this->getBucket();
			$callbackData->setBucket($this->getBucket());
		}
		
		if (!$fields)
		{
			throw new SquanchException('Fields are not set');
		}
		
		/** @var Data[] $query */
		$query = $this->getConnector()->loadAllByFields($fields, [], $limit);
		
		if ($query)
		{
			$data = [];
			$result = false;
			
			foreach ($query as $item)
			{
				if ($item->EndDate > new \DateTime()) {
					$data[] = $item;
					$result = true;
					
					$this->data = $item;
					$this->updateTTLIfNeed();
					$this->data = null;
				}
			}
			
			if ($result)
			{
				$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_GET, $callbackData);
				$result = new CollectionHandler($data);
				
			}
			else
			{
				$this->getCallbacksLoader()->executeCallback(Callbacks::MISS_ON_GET, $callbackData);
				$result = new CollectionHandler([]);
			}
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
		$callbackData = (new CallbackData());
		
		$fields = [];
		
		if ($this->getKey())
		{
			$fields['Id'] = $this->getKey();
			$callbackData->setKey($this->getKey());
		}
		
		if ($this->getBucket())
		{
			$fields['Bucket'] = $this->getBucket();
			$callbackData->setBucket($this->getBucket());
		}
		
		if (!$fields)
		{
			throw new SquanchException('Fields are not set');
		}
		
		$this->data = $this->getConnector()->loadOneByFields($fields);
		
		if ($this->data && $this->data->EndDate > new \DateTime())
		{
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