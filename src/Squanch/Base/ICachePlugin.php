<?php
namespace Squanch\Base;


use Squanch\Enum\Bucket;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;


interface ICachePlugin
{
	public function setCallbacksLoader(ICallbacksLoader $callbacksLoader): ICachePlugin;
	
	public function getCallbacksLoader(): ICallbacksLoader;
	
	public function delete(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdDelete;
	
	public function get(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdGet;
	
	public function has(string $key = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdHas;
	
	public function set(string $key = null, $data = null, string $bucketName = Bucket::DEFAULT_BUCKET_NAME): ICmdSet;
}