<?php
namespace Squanch\Plugins\LRUCache;


use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;

use Squanch\Base\ICachePlugin;
use Squanch\Plugins\AbstractPlugin;
use Squanch\Plugins\LRUCache\Base\ILRUAdapter;
use Squanch\Plugins\LRUCache\Command\Delete;
use Squanch\Plugins\LRUCache\Command\Get;
use Squanch\Plugins\LRUCache\Command\Has;
use Squanch\Plugins\LRUCache\Command\Set;


class LRUCachePlugin extends AbstractPlugin implements ICachePlugin
{
	/** @var ILRUAdapter */
	private $lruAdapter;
	
	
	public function __construct($capacity)
	{
		parent::__construct();
		$this->lruAdapter = new LRUAdapter($capacity);
	}


	protected function getCmdGet(): ICmdGet
	{
		return new Get($this->lruAdapter);
	}

	protected function getCmdHas(): ICmdHas
	{
		return new Has($this->lruAdapter);
	}

	protected function getCmdDelete(): ICmdDelete
	{
		return new Delete($this->lruAdapter);
	}

	protected function getCmdSet(): ICmdSet
	{
		return new Set($this->lruAdapter);
	}
}