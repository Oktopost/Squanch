<?php
namespace Squanch\AbstractCommand;


use Objection\Mapper;
use Objection\LiteObject;

use Squanch\Enum\Bucket;
use Squanch\Enum\Callbacks;
use Squanch\Base\ICallback;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Boot\ICallbacksLoader;


abstract class AbstractSet implements ICmdSet
{
	private $connector;
	private $callbacksLoader;
	private $insertOnly;
	private $updateOnly;
	private $key;
	private $bucket = Bucket::DEFAULT_BUCKET_NAME;
	private $data;
	private $ttl;
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	protected function getConnector()
	{
		return $this->connector;
	}	
	
	protected function reset()
	{
		$this->insertOnly = false;
		$this->updateOnly = false;
		unset($this->key);
		unset($this->data);
		unset($this->ttl);
		$this->bucket = Bucket::DEFAULT_BUCKET_NAME;
	}
	
	
	/**
	 * @return mixed
	 */
	protected function getTTL()
	{
		return $this->ttl;
	}
	
	protected function getBucket(): string
	{
		return $this->bucket;
	}
	
	protected function getKey(): string
	{
		return $this->key;
	}
	
	/**
	 * @return mixed
	 */
	protected function getData()
	{
		return $this->data;
	}
	
	protected function getJsonData(): string
	{
		$mapperForArrayOfLiteObjects = null;
		
		if (is_array($this->getData()))
		{
			$data = $this->getData();
			
			foreach ($data as $key => $value)
			{
				if ($value instanceof LiteObject)
				{
					$mapperForArrayOfLiteObjects = Mapper::createFor(get_class($value));
				}
				
				break;
			}
		}		
		
		if ($this->getData() instanceof LiteObject)
		{
			$mapper = Mapper::createFor(
				get_class($this->getData())
			);
			
			return $mapper->getJson($this->getData());
		}
		else if($mapperForArrayOfLiteObjects)
		{
			return $mapperForArrayOfLiteObjects->getJson($this->getData());
		}
		else if(is_scalar($this->getData()))
		{
			return $this->getData();
		}
		else
		{
			return json_encode($this->getData());
		}
	}
	
	
	/**
	 * @return static
	 */
	public function setup($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
		return $this;
	}
	
	/**
	 * @return static
	 */
	public function setBucket(string $bucket)
	{
		$this->bucket = $bucket;
		return $this;
	}
	
	/**
	 * @return static
	 */
	public function setKey(string $key)
	{
		$this->key = $key;
		return $this;
	}
	
	/**
	 * @return static
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
	
	public function setTTL(int $ttl)
	{
		$this->ttl = $ttl;
		
		return $this;
	}
	
	public function setForever()
	{
		$this->ttl = -1;
		
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onSuccess
	 * @return static
	 */
	public function onSuccess($onSuccess)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::SUCCESS_ON_SET, $onSuccess);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onFail
	 * @return static
	 */
	public function onFail($onFail)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::FAIL_ON_SET, $onFail);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onComplete
	 * @return static
	 */
	public function onComplete($onComplete)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::ON_SET, $onComplete);
		return $this;
	}
	
	public function insertOnly()
	{
		$this->insertOnly = true;
		$this->updateOnly = false;
		
		return $this;
	}
	
	public function updateOnly()
	{
		$this->insertOnly = false;
		$this->updateOnly = true;
		
		return $this;
	}
	
	public function isInsertOnly()
	{
		return $this->insertOnly == true;
	}
	
	public function isUpdateOnly()
	{
		return $this->updateOnly == true;
	}
}