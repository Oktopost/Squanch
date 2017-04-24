<?php
namespace dummyStorage;


use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Plugins\AbstractPlugin;


class DummyStoragePlugin extends AbstractPlugin implements ICachePlugin
{
	private $connector;
	
	
	protected function getConnector()
	{
		return $this->connector;
	}
	
	protected function getCmdGet(): ICmdGet
	{
		return new Command\CmdGet();
	}
	
	protected function getCmdHas(): ICmdHas
	{
		return new Command\CmdHas();
	}
	
	protected function getCmdDelete(): ICmdDelete
	{
		return new Command\CmdDelete();
	}
	
	protected function getCmdSet(): ICmdSet
	{
		return new Command\CmdSet();
	}
	
	
	public function __construct()
	{
		$this->connector = new DummyConnector();
	}
}