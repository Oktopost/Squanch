<?php
namespace Squanch\Plugins;


use Squanch\Enum\Bucket;
use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;


abstract class AbstractPlugin implements ICachePlugin
{
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	
	
	protected abstract function getConnector();
	
	protected abstract function getCmdGet(): ICmdGet;
	
	protected abstract function getCmdHas(): ICmdHas;
	
	protected abstract function getCmdDelete(): ICmdDelete;
	
	protected abstract function getCmdSet(): ICmdSet;
	
	
	public function setCallbacksLoader(ICallbacksLoader $callbacksLoader): ICachePlugin
	{
		$this->callbacksLoader = $callbacksLoader;
		return $this;
	}
	
	public function delete(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdDelete
	{
		$result= $this->getCmdDelete()->setup($this->getConnector(), $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		if ($bucketName)
			$result->byBucket($bucketName);
		
		return $result;
	}
	
	public function get(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdGet
	{
		$result = $this->getCmdGet()->setup($this->getConnector(), $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		if ($bucketName)
			$result->byBucket($bucketName);
		
		return $result;
	}
	
	public function has(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdHas
	{
		$result = $this->getCmdHas()->setup($this->getConnector(), $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		if ($bucketName)
			$result->byBucket($bucketName);
		
		return $result;
	}
	
	public function set(string $key = null, $data = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdSet
	{
		$result = $this->getCmdSet()->setup($this->getConnector(), $this->callbacksLoader);
		
		if ($key)
			$result->setKey($key);
		
		if ($data)
			$result->setData($data);
		
		if ($bucketName)
			$result->setBucket($bucketName);
		
		return $result;
	}
}