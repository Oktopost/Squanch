<?php
namespace Squanch\Plugins\Squid;


use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\ICachePlugin;
use Squanch\Plugins\AbstractPlugin;
use Squanch\Plugins\Squid\Base\ISquanchSquidConnector;


class SquidPlugin extends AbstractPlugin implements ICachePlugin
{
	/** @var ISquanchSquidConnector */
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
	
	
	public function __construct(ISquanchSquidConnector $mysqlObjectConnector)
	{
		$this->connector = $mysqlObjectConnector;
	}
}