<?php
namespace Squanch\Plugins\InMemoryCache;


use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;

use Squanch\Plugins\AbstractPlugin;


class InMemoryPlugin extends AbstractPlugin
{
	private $storage;
	
	
	public function __construct()
	{
		$this->storage = new \stdClass();
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