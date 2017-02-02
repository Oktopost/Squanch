<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Enum\Callbacks;
use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractSet;

use Squid\MySql\Connectors\IMySqlObjectConnector;

use Objection\Mapper;
use Objection\LiteObject;


class Set extends AbstractSet implements ICmdSet
{
	/** @var IMySqlObjectConnector */
	private $connector;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	
	
	private function checkExists(): bool
	{
		$has = new Has($this->connector, $this->callbacksLoader);
		
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
		$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_SET, $this->getCallbackData($data));
	}
	
	private function onCompleteCallback(Data $data)
	{
		$this->callbacksLoader->executeCallback(Callbacks::ON_SET, $this->getCallbackData($data));
	}
	
	private function onSuccessCallback($data)
	{
		$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_SET, $this->getCallbackData($data));
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
		$data = new Data();
		$data->Id = $this->getKey();
		$data->Bucket = $this->getBucket();
		
		if ($this->getData() instanceof LiteObject)
		{
			$mapper = Mapper::createFor(
				get_class($this->getData())
			);
				
			$data->Value = $mapper->getJson($this->getData());
		}
		else if(is_scalar($this->getData()))
		{
			$data->Value = $this->getData();
		}
		else
		{
			$data->Value = json_encode($this->getData());
		}
		
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
		
		$result = $this->connector->upsertByFields($data, ['Id', 'Bucket']);
		
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