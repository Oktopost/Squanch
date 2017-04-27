<?php
namespace Squanch\Plugins\Squid\Connector;


use Squid\MySql\Impl\Connectors\MySqlObjectConnector;


interface ISquidCacheConnector
{
	/**
	 * @return static
	 */
	public function setConnector(MySqlObjectConnector $conn): ISquidCacheConnector;

	/**
	 * @return static
	 */
	public function setTableName(string $tableName): ISquidCacheConnector;
}