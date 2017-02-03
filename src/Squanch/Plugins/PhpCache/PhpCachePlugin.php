<?php
namespace Squanch\Plugins\PhpCache;

use Squanch\Enum\Bucket;
use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;

use Psr\Cache\CacheItemPoolInterface;


class PhpCachePlugin implements ICachePlugin
{
	/** @var CacheItemPoolInterface */
	private $connector;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	
	
	public function __construct(CacheItemPoolInterface $connector)
	{
		$this->connector = $connector;
	}
	
	
	public function setCallbacksLoader(ICallbacksLoader $callbacksLoader): ICachePlugin
	{
		$this->callbacksLoader = $callbacksLoader;
		return $this;
	}
	
	public function delete(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdDelete
	{
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