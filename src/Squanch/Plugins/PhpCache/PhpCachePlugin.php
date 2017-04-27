<?php
namespace Squanch\Plugins\PhpCache;


use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Plugins\AbstractPlugin;

use Cache\Hierarchy\HierarchicalPoolInterface;


class PhpCachePlugin extends AbstractPlugin implements ICachePlugin
{
	/** @var HierarchicalPoolInterface */
	private $connector;
	
	
	protected function getCmdGet(): ICmdGet
	{
		return (new Command\Get())->setConnector($this->connector);
	}
	
	protected function getCmdHas(): ICmdHas
	{
		return (new Command\Has())->setConnector($this->connector);
	}
	
	protected function getCmdDelete(): ICmdDelete
	{
		return (new Command\Delete())->setConnector($this->connector);
	}
	
	protected function getCmdSet(): ICmdSet
	{
		return (new Command\Set())->setConnector($this->connector);
	}
	
	
	public function __construct(HierarchicalPoolInterface $connector)
	{
		$this->connector = $connector;
	}
}