<?php
namespace Squanch\Plugins\PhpCache\Connector;


use Cache\Hierarchy\HierarchicalPoolInterface;


trait TPhpCacheConnector
{
	/** @var HierarchicalPoolInterface */
	private $_conn;
	
	
	protected function getConnector(): HierarchicalPoolInterface
	{
		return $this->_conn;
	}
	
	
	/**
	 * @return static
	 */
	public function setConnector(HierarchicalPoolInterface $client): IPhpCacheConnector
	{
		$this->_conn = $client;
		return $this;
	}
}