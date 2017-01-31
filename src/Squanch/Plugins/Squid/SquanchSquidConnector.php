<?php
namespace Squanch\Plugins\Squid;


use Squid\MySql\Impl\Connectors\MySqlObjectConnector;


class SquanchSquidConnector extends MySqlObjectConnector
{
	private $tableName;
	
	
	public function setTable($tableName)
	{
		parent::setTable($tableName);
		$this->tableName = $tableName;
		return $this;
	}
	
	public function deleteByFields(array $fields)
	{
		$delete = $this->getConnector()
			->delete()
			->from($this->tableName);
		
		$this->createFilter($delete, $fields);
		
		return $delete->executeDml(true);
	}
}