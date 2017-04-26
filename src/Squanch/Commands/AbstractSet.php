<?php
namespace Squanch\Commands;


use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Callbacks\Events\ISetEvent;
use Squanch\Objects\Data;

use Objection\Mapper;
use Objection\LiteObject;


abstract class AbstractSet implements ICmdSet
{
	private $data;
	
	/** @var ISetEvent */
	private $event;
	
	
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

	public function __construct()
	{
		$this->data = new Data();
	}
	
	
	public function setSetEvents(ISetEvent $event)
	{
		$this->event = $event;
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
	 * @return bool
	 */
	public function insert()
	{
		if ($this->onInsert($this->data))
		{
			$this->event->triggerInsert($this->data);
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function update()
	{
		if ($this->onUpdate($this->data))
		{
			$this->event->triggerUpdate($this->data);
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function save()
	{
		if ($this->onSave($this->data))
		{
			$this->event->triggerSave($this->data);
			return true;
		}
		
		return false;
	}
}