<?php
namespace Squanch\Plugins\Squid\Connector;


use Squid\MySql\Impl\Connectors\MySqlObjectConnector;


interface ISquidConnector
{
	/**
	 * @return static
	 */
	public function setConnector(MySqlObjectConnector $conn, string $table): ISquidConnector;
}