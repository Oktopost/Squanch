<?php
namespace Squanch\Plugins\Squid;


use Squanch\Enum\Bucket;
use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;

use Squanch\Plugins\Squid\Base\ISquanchSquidConnector;


class SquidPlugin implements ICachePlugin
{
	/** @var ISquanchSquidConnector */
	private $connector;
	
	/** @var ICallbacksLoader */
	private $callbacksLoader;
	
	
	public function __construct(ISquanchSquidConnector $mysqlObjectConnector)
	{
		$this->connector = $mysqlObjectConnector;
	}
	
	
	public function setCallbacksLoader(ICallbacksLoader $callbacksLoader): ICachePlugin
	{
		$this->callbacksLoader = $callbacksLoader;
		return $this;
	}
	
	public function delete(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdDelete
	{
		/** @var ICmdDelete $result */
		$result = new Command\Delete($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		if ($bucketName)
			$result->byBucket($bucketName);
		
		return $result;
	}
	
	public function get(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdGet
	{
		/** @var ICmdGet $result */
		$result = new Command\Get($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		if ($bucketName)
			$result->byBucket($bucketName);
		
		return $result;
	}
	
	public function has(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdHas
	{
		/** @var ICmdHas $result */
		$result = new Command\Has($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->byKey($key);
		
		if ($bucketName)
			$result->byBucket($bucketName);
		
		return $result;
	}
	
	public function set(string $key = null, $data = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdSet
	{
		/** @var ICmdSet $result */
		$result = new Command\Set($this->connector, $this->callbacksLoader);
		
		if ($key)
			$result->setKey($key);
		
		if ($data)
			$result->setData($data);
		
		if ($bucketName)
			$result->setBucket($bucketName);
		
		return $result;
	}
}