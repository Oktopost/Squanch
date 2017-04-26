<?php
namespace Squanch\Plugins\Predis\Connector;


use Predis\Client;


interface IPredisConnector
{
	/**
	 * @param Client $client
	 * @return static
	 */
	public function setClient(Client $client): IPredisConnector;
}