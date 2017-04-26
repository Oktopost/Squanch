<?php
namespace Squanch\Plugins\Predis\Connector;


use Predis\Client;


trait TPredisConnector
{
	/** @var Client */
	private $_client;
	
	
	protected function getClient(): Client
	{
		return $this->_client;
	}
	
	
	/**
	 * @param Client $client
	 * @return static
	 */
	public function setClient(Client $client): IPredisConnector
	{
		$this->_client = $client;
		return $this;
	}
}