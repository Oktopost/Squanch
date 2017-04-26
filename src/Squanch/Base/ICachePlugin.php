<?php
namespace Squanch\Base;


use Squanch\Base\Callbacks\ICacheEvents;
use Squanch\Base\Callbacks\ICacheEventsConsumer;

use Squanch\Enum\Bucket;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;


interface ICachePlugin
{
	public function getEvents(): ICacheEventsConsumer;
	public function setEventManager(ICacheEvents $events): ICachePlugin;
	
	public function delete(string $key, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdDelete;
	public function get(string $key, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdGet;
	public function has(string $key, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdHas;
	public function set(string $key, $data = null, string $bucket = Bucket::DEFAULT_BUCKET_NAME): ICmdSet;
}