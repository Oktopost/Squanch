<?php
namespace Squanch\Plugins\Squid\Connector;


use Squid\MySql\IMySqlConnector;
use Squid\MySql\Impl\Connectors\MySqlObjectConnector;


trait TSquidCacheConnector
{
	private $_table;
	
	/** @var MySqlObjectConnector */
	private $_conn;
	
	
	protected function getConnector(): MySqlObjectConnector
	{
		return $this->_conn;
	}
	
	protected function getTableName(): string
	{
		return $this->_table;
	}
	
	protected function getMysqlConnector(): IMySqlConnector
	{
		return $this->_conn->getConnector();
	}
	
	
	/**
	 * @return static
	 */
	public function setConnector(MySqlObjectConnector $conn): ISquidCacheConnector
	{
		$this->_conn = $conn;
		return $this;
	}

	/**
	 * @return static
	 */
	public function setTableName(string $tableName): ISquidCacheConnector
	{
		$this->_table = $tableName;
		return $this;
	}
}