<?php
namespace dummyStorage;


class DummyConnector
{
	private $db = [];
	
	/**
	 * @return array
	 */
	public function getDb(): array
	{
		return $this->db;
	}
	
	/**
	 * @param array $db
	 */
	public function setDb(array $db)
	{
		$this->db = $db;
	}
	
}