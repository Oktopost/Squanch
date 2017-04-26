<?php
namespace Squanch\Plugins;


use Squanch\Enum\Bucket;
use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\IWhere;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Callbacks\ICacheEvents;
use Squanch\Base\Callbacks\ICacheEventsConsumer;


abstract class AbstractPlugin implements ICachePlugin
{
	/** @var ICacheEvents */
	private $event;
	
	
	private function setupWhere(IWhere $where, string $bucket, string $key)
	{
		if ($bucket) $where->byBucket($bucket);
		if ($key) $where->byKey($key);
		
		return $this;
	}
	
	
	protected abstract function getCmdGet(): ICmdGet;
	protected abstract function getCmdHas(): ICmdHas;
	protected abstract function getCmdDelete(): ICmdDelete;
	protected abstract function getCmdSet(): ICmdSet;
	
	
	public function getEvents(): ICacheEventsConsumer
	{
		return $this->event;
	}

	public function setEventManager(ICacheEvents $events): ICachePlugin
	{
		$this->event = $events;
		return $this;
	}
	
	public function delete(string $key = null, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdDelete
	{
		$delete = $this->getCmdDelete();
		$delete->setDeleteEvents($this->event->deleteEvent());
		return $this->setupWhere($delete, $bucket, $key);
	}
	
	public function get(string $key = null, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdGet
	{
		$get = $this->getCmdGet();
		$get->setGetEvents($this->event->getEvent());
		return $this->setupWhere($get, $bucket, $key);
	}
	
	public function has(string $key = null, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdHas
	{
		$has = $this->getCmdHas();
		$has->setHasEvents($this->event->hasEvent());
		return $this->setupWhere($has, $bucket, $key);
	}
	
	public function set(string $key = null, $data = null, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdSet
	{
		$set = $this->getCmdSet();
		$set->setSetEvents($this->event->setEvent());
		
		if ($key)
			$set->setKey($key);
		
		if ($data)
			$set->setData($data);
		
		if ($bucket)
			$set->setBucket($bucket);
		
		return $set;
	}
}