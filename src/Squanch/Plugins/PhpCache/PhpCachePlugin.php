<?php
namespace Squanch\Plugins\PhpCache;


use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Plugins\AbstractPlugin;

use Psr\Cache\CacheItemPoolInterface;


class PhpCachePlugin extends AbstractPlugin implements ICachePlugin
{
	/** @var CacheItemPoolInterface */
	private $connector;
	
	
	
	protected function getConnector()
	{
		return $this->connector;
	}
	
	protected function getCmdGet(): ICmdGet
	{
		return new Command\Get();
	}
	
	protected function getCmdHas(): ICmdHas
	{
		return new Command\Has();
	}
	
	protected function getCmdDelete(): ICmdDelete
	{
		return new Command\Delete();
	}
	
	protected function getCmdSet(): ICmdSet
	{
		return new Command\Set();
	}
	
	
	public function __construct(CacheItemPoolInterface $connector)
	{
		$this->connector = $connector;
	}
	
	
}