<?php
namespace Squanch\Plugins\Squid;


use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Objects\Data;
use Squanch\Plugins\AbstractPlugin;

use Squid\MySql;
use Squid\MySql\IMySqlConnector;
use Squid\MySql\Impl\Connectors\MySqlObjectConnector;


class SquidPlugin extends AbstractPlugin implements ICachePlugin
{
	private $table;
	
	/** @var MySqlObjectConnector */
	private $connector;
	
	
	protected function getCmdGet(): ICmdGet
	{
		return (new Command\Get())->setConnector($this->connector)->setTableName($this->table);
	}
	
	protected function getCmdHas(): ICmdHas
	{
		return (new Command\Has())->setConnector($this->connector)->setTableName($this->table);
	}
	
	protected function getCmdSet(): ICmdSet
	{
		return (new Command\Set())->setConnector($this->connector)->setTableName($this->table);
	}
	
	protected function getCmdDelete(): ICmdDelete
	{
		return (new Command\Delete())->setConnector($this->connector)->setTableName($this->table);
	}


	/**
	 * SquidPlugin constructor.
	 * @param IMySqlConnector|array $connection
	 * @param string $table
	 */
	public function __construct($connection, string $table)
	{
		parent::__construct();
		
		if (is_array($connection))
		{
			$mysql = new MySql();
			$mysql->config()->addConfig('main', $connection);
			$connection = $mysql->getConnector('main');
		}
		
		$this->table = $table;
		$this->connector = new MySqlObjectConnector();
		$this->connector
			->setTable($table)
			->setDomain(Data::class)
			->setConnector($connection);
	}
}