<?php
namespace Squanch\Plugins\Predis;


use Squanch\Enum\Bucket;
use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;

use Predis\Client;


class PredisPlugin implements ICachePlugin
{
	/** @var Client */
	private $connector;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	
	
	public function __construct(Client $client)
	{
		$this->connector = $client;
	}
	
	
	public function setCallbacksLoader(ICallbacksLoader $callbacksLoader): ICachePlugin
	{
		$this->callbacksLoader = $callbacksLoader;
		return $this;
	}
	
	public function delete(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdDelete
	{
		/** @var ICmdDelete $result */
		$result = new Command\Delete();
		$result->setup($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		if ($bucketName)
			$result->byBucket($bucketName);
		
		return $result;
	}
	
	public function get(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdGet
	{
		/** @var ICmdGet $result */
		$result = new Command\Get();
		$result->setup($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		if ($bucketName)
			$result->byBucket($bucketName);
		
		return $result;
	}
	
	public function has(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdHas
	{
		/** @var ICmdHas $result */
		$result = new Command\Has();
		$result->setup($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		if ($bucketName)
			$result->byBucket($bucketName);
		
		return $result;
	}
	
	public function set(string $key = null, $data = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdSet
	{
		/** @var ICmdSet $result */
		$result = new Command\Set();
		$result->setup($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->setKey($key);
		
		if ($data)
			$result->setData($data);
		
		if ($bucketName)
			$result->setBucket($bucketName);
		
		return $result;
	}
}