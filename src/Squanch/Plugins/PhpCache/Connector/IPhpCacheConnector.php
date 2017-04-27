<?php
namespace Squanch\Plugins\PhpCache\Connector;


use Cache\Hierarchy\HierarchicalPoolInterface;


interface IPhpCacheConnector
{
	/**
	 * @return static
	 */
	public function setConnector(HierarchicalPoolInterface $conn): IPhpCacheConnector;
}