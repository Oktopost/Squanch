<?php

namespace Squanch\Commands;


use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Base\Command\ICmdGet;
use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Callbacks\CallbacksHandler;

use Objection\Mapper;
use Objection\LiteObject;


abstract class AbstractGet implements ICmdGet
{
	use \Squanch\Commands\Traits\TWhere;
	use \Squanch\Commands\Traits\TResetTTL;
	
	
	private $connector;
	
	/** @var CallbacksHandler */
	private $callbacksHandler;


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
		$this->callbacksHandler->onGetRequest(is_null($result), $data);
		
		if ($result && $this->hasTTL())
		{
			$result->setTTL($this->getTTL());
			$this->onUpdateTTL($data, $this->getTTL());
		}
		
		return !is_null($result);
	}
	
	
	protected function getConnector()
	{
		return $this->connector;
	}
	
	
	/**
	 * @param CallbackData $data
	 * @return Data|null
	 */
	protected abstract function onGet(CallbackData $data);
	
	protected abstract function onUpdateTTL(CallbackData $data, int $newTTL);
	
	
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
	 * @return array|bool
	 */
	public function asArray()
	{
		if (!$this->tryGet($this->dataObject(), $data))
			return false;
		
		$data = json_decode($data->Value, true);
		return is_array($data) ? $data : false;
	}

	/**
	 * @return \stdClass|bool
	 */
	public function asObject()
	{
		if ($this->tryGet($this->dataObject(), $data))
			return false;
		
		$data = json_decode($data->Value, false);
		return is_object($data) ? $data : false;
	}

	/**
	 * @return LiteObject|bool
	 */
	public function asLiteObject(string $class)
	{
		if (!$this->tryGet($this->dataObject(), $data))
			return false;
		
		$mapper = Mapper::createFor($class);
		return $mapper->getObject($data->Value, $class);
	}

	/**
	 * @return LiteObject[]|bool
	 */
	public function asArrayOfLiteObjects(string $class)
	{
		if (!$this->tryGet($this->dataObject(), $data))
			return false;
		
		$mapper = Mapper::createFor($class);
		return $mapper->getObjects($data->Value, $class);
	}

	/**
	 * @return string|bool
	 */
	public function asString()
	{
		return ($this->tryGet($this->dataObject(), $data) ? 
			$data->Value :
			false);
	}

	/**
	 * @return Data|bool
	 */
	public function asData()
	{
		return ($this->tryGet($this->dataObject(), $data) ? $data : false);
	}
}