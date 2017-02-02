<?php
namespace Squanch\Decorators\Migration;


use Squanch\Enum\Bucket;
use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;


class MigrationDecorator implements ICachePlugin
{
	private $main;
	private $fallback;
	
	/** @var ICallbacksLoader */
	private $callbackLoader;
	
	
	public function __construct(ICachePlugin $main, ICachePlugin $fallback)
	{
		$this->main = $main;
		$this->fallback = $fallback;
	}
	
	
	public function setCallbacksLoader(ICallbacksLoader $callbacksLoader): ICachePlugin
	{
		$this->main->setCallbacksLoader($callbacksLoader);
		$this->fallback->setCallbacksLoader($callbacksLoader);
		$this->callbackLoader = $callbacksLoader;
		
		return $this;
	}
	
	public function delete(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdDelete
	{
		return $this->main->delete($key, $bucketName);
	}
	
	public function get(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdGet
	{
		$get = new MigrationGet($this->main, $this->fallback);
		$get->setup(null, $this->callbackLoader);
		
		if ($key)
			$get->byKey($key);
		
		if($bucketName)
			$get->byBucket($bucketName);
		
		return $get;
	}
	
	public function has(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdHas
	{
		return $this->main->has($key, $bucketName);
	}
	
	public function set(string $key = null, $data = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdSet
	{
		return $this->main->set($key, $data, $bucketName);
	}
}