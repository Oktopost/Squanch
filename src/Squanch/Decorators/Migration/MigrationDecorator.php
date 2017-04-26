<?php
namespace Squanch\Decorators\Migration;


use Squanch\Base\Callbacks\ICacheEvents;
use Squanch\Base\Callbacks\ICacheEventsConsumer;
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
	
	
	public function __construct(ICachePlugin $main, ICachePlugin $fallback)
	{
		$this->main = $main;
		$this->fallback = $fallback;
	}
	
	
	public function delete(string $key = null, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdDelete
	{
		return $this->main->delete($key, $bucket);
	}
	
	public function get(string $key = null, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdGet
	{
		$get = new MigrationGet($this->main, $this->fallback);
		$get->setup(null, $this->callbackLoader);
		
		if ($key)
			$get->byKey($key);
		
		if($bucket)
			$get->byBucket($bucket);
		
		return $get;
	}
	
	public function has(string $key = null, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdHas
	{
		return $this->main->has($key, $bucket);
	}
	
	public function set(string $key = null, $data = null, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdSet
	{
		return $this->main->set($key, $data, $bucket);
	}

	public function getEvents(): ICacheEventsConsumer
	{
		return null;
	}

	public function setEventManager(ICacheEvents $events): ICachePlugin
	{
		return $this;
	}
}