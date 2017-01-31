<?php
namespace dummyStorage;


use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;


class DummyStoragePlugin implements ICachePlugin
{
	private $connector;
	private $callbacksLoader;
	
	
	public function __construct()
	{
		$this->connector = new DummyConnector();
	}
	
	
	public function setCallbacksLoader(ICallbacksLoader $callbacksLoader): ICachePlugin
	{
		$this->callbacksLoader = $callbacksLoader;
		return $this;
	}
	
	public function delete(string $key = null): ICmdDelete
	{
		/** @var ICmdDelete $result */
		$result = new Command\CmdDelete($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		return $result;
	}
	
	public function get(string $key = null): ICmdGet
	{
		/** @var ICmdGet $result */
		$result = new Command\CmdGet($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		return $result;
	}
	
	public function has(string $key = null): ICmdHas
	{
		/** @var ICmdHas $result */
		$result = new Command\CmdHas($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		return $result;
	}
	
	public function set(string $key = null, $data = null): ICmdSet
	{
		/** @var ICmdSet $result */
		$result = new Command\CmdSet($this->connector, $this->callbacksLoader);
		
		if($key)
			$result->setKey($key);
			
		if ($data)
			$result->setData($data);
		
		return $result;
	}
}