<?php
namespace Squanch\Commands;


use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Base\Command\ICmdSet;
use Squanch\Objects\CallbackData;
use Squanch\Objects\Data;
use Squanch\Callbacks\CallbacksHandler;

use Objection\Mapper;
use Objection\LiteObject;


abstract class AbstractSet implements ICmdSet
{
	private $data;
	private $connector;
	
	/** @var CallbacksHandler */
	private $callbacksHandler;
	
	
	private function getJsonData($data): string
	{
		$mapperForArrayOfLiteObjects = null;
		
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				if ($value instanceof LiteObject)
				{
					$mapperForArrayOfLiteObjects = Mapper::createFor(get_class($value));
				}
				
				break;
			}
		}		
		
		if ($data instanceof LiteObject)
		{
			$mapper = Mapper::createFor(get_class($data));
			return $mapper->getJson($data);
		}
		else if ($mapperForArrayOfLiteObjects)
		{
			return $mapperForArrayOfLiteObjects->getJson($data);
		}
		else if (is_scalar($data))
		{
			return $data;
		}
		else
		{
			return json_encode($data);
		}
	}
	
	private function processResult(bool $result): bool
	{
		$this->callbacksHandler->onSetRequest($result, 
			new CallbackData($this->data->Bucket, $this->data->Id));
		return $result;
	}
	
	
	public function __construct()
	{
		$this->data = new Data();
	}


	/**
	 * @return static
	 */
	public function setup($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksHandler = new CallbacksHandler($callbacksLoader);
		return $this;
	}


	/**
	 * @return static
	 */
	public function setBucket(string $bucket)
	{
		$this->data->Bucket = $bucket;
		return $this;
	}

	/**
	 * @return static
	 */
	public function setKey(string $key)
	{
		$this->data->Id = $key;
		return $this;
	}

	/**
	 * @return static
	 */
	public function setData($data)
	{
		$this->data->Value = $this->getJsonData($data);
		return $this;
	}

	/**
	 * @return static
	 */
	public function setTTL(int $ttl)
	{
		$this->data->setTTL($ttl);
		return $this;
	}

	/**
	 * @return static
	 */
	public function setForever()
	{
		$this->data->setTTL(-1);
		return $this;
	}
	
	
	protected abstract function onInsert(Data $data): bool;
	protected abstract function onUpdate(Data $data): bool;
	protected abstract function onSave(Data $data): bool;
	
	
	/**
	 * @return static
	 */
	public function insert()
	{
		return $this->processResult($this->onInsert($this->data));
	}
	
	/**
	 * @return static
	 */
	public function update()
	{
		return $this->processResult($this->onUpdate($this->data));
	}
	
	/**
	 * @return static
	 */
	public function save()
	{
		return $this->processResult($this->onSave($this->data));
	}
}