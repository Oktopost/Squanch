<?php
namespace Squanch\Plugins\InMemoryCache;


use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;

use Squanch\Plugins\AbstractPlugin;
use Squanch\Plugins\InMemoryCache\Base\IStorage;


class InMemoryPlugin extends AbstractPlugin
{
	/** @var IStorage */
	private $storage;
	
	
	public function __construct()
	{
		// TODO: $this->storage = new Storage();
	}


	protected function getCmdGet(): ICmdGet
	{
		// TODO: Implement getCmdGet() method.
	}

	protected function getCmdHas(): ICmdHas
	{
		// TODO: Implement getCmdHas() method.
	}

	protected function getCmdDelete(): ICmdDelete
	{
		// TODO: Implement getCmdDelete() method.
	}

	protected function getCmdSet(): ICmdSet
	{
		// TODO: Implement getCmdSet() method.
	}
}