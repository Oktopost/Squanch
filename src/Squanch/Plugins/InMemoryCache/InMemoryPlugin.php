<?php
namespace Squanch\Plugins\InMemoryCache;


use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;

use Squanch\Plugins\AbstractPlugin;
use Squanch\Plugins\InMemoryCache\Base\IStorage;
use Squanch\Plugins\InMemoryCache\Command\Delete;
use Squanch\Plugins\InMemoryCache\Command\Get;
use Squanch\Plugins\InMemoryCache\Command\Has;
use Squanch\Plugins\InMemoryCache\Command\Set;


class InMemoryPlugin extends AbstractPlugin
{
	/** @var IStorage */
	private $storage;
	
	
	public function __construct()
	{
		parent::__construct();
		$this->storage = new Storage();
	}


	protected function getCmdGet(): ICmdGet
	{
		return new Get($this->storage);
	}

	protected function getCmdHas(): ICmdHas
	{
		return new Has($this->storage);
	}

	protected function getCmdDelete(): ICmdDelete
	{
		return new Delete($this->storage);
	}

	protected function getCmdSet(): ICmdSet
	{
		return new Set($this->storage);
	}
}