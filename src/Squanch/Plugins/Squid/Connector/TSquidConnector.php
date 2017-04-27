<?php
namespace Squanch\Plugins\Squid\Connector;


use Squid\MySql\Impl\Connectors\MySqlObjectConnector;


trait TSquidConnector
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
	
	
	/**
	 * @return static
	 */
	public function setConnector(MySqlObjectConnector $client, string $table): ISquidConnector
	{
		$this->_conn = $client;
		$this->_table = $table;
		return $this;
	}
}