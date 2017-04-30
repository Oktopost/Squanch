<?php
namespace Squanch\Commands;


use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Callbacks\Events\IGetEvent;
use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;

use Objection\Mapper;
use Objection\LiteObject;


abstract class AbstractGet implements ICmdGet
{
	use \Squanch\Commands\Traits\TWhere;
	use \Squanch\Commands\Traits\TResetTTL;
	
	
	/** @var IGetEvent */
	private $event;


	/**
	 * @param CallbackData $data
	 * @param Data|null $result
	 * @return bool
	 */
	private function tryGet(CallbackData $data, Data &$result = null): bool
	{
		if (!$data->Key)
			throw new \Exception('Key must be set for the get operation!');
		
		$result = $this->onGet($data);
		
		if (is_null($result))
		{
			$this->event->triggerMiss($data->Bucket, $data->Key);
		}
		else
		{
			$this->event->triggerHit($result);
		}
		
		if ($result && $this->hasTTL())
		{
			$result->setTTL($this->getTTL());
			$this->onUpdateTTL($data, $this->getTTL());
		}
		
		return !is_null($result);
	}
	
	
	/**
	 * @param CallbackData $data
	 * @return Data|null
	 */
	protected abstract function onGet(CallbackData $data);
	
	protected abstract function onUpdateTTL(CallbackData $data, int $ttl);
	
	
	
	public function setGetEvents(IGetEvent $event)
	{
		$this->event = $event;
	}
	
	/**
	 * @return array|null
	 */
	public function asArray()
	{
		if (!$this->tryGet($this->dataObject(), $data))
			return null;
		
		$data = json_decode($data->Value, true);
		// TODO: Throw exception if not array
		return is_array($data) ? $data : null;
	}

	/**
	 * @return \stdClass|null
	 */
	public function asObject()
	{
		if (!$this->tryGet($this->dataObject(), $data))
			return null;
		
		$data = json_decode($data->Value, false);
		
		if (!is_object($data))
			throw new \Exception('Data must be a valid json!');
		
		return $data;
	}

	/**
	 * @return LiteObject|null
	 */
	public function asLiteObject(string $class)
	{
		if (!$this->tryGet($this->dataObject(), $data))
			return null;
		
		$mapper = Mapper::createFor($class);
		return $mapper->getObject($data->Value, $class);
	}

	/**
	 * @return LiteObject[]|null
	 */
	public function asLiteObjects(string $class)
	{
		if (!$this->tryGet($this->dataObject(), $data))
			return null;
		
		$mapper = Mapper::createFor($class);
		return $mapper->getObjects(json_decode($data->Value), $class);
	}

	/**
	 * @return string|null
	 */
	public function asString()
	{
		return ($this->tryGet($this->dataObject(), $data) ? 
			$data->Value :
			null);
	}

	/**
	 * @return Data|null
	 */
	public function asData()
	{
		return ($this->tryGet($this->dataObject(), $data) ? $data : null);
	}
}