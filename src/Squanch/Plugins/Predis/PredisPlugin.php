<?php
namespace Squanch\Plugins\Predis;


use Squanch\Base\Callbacks\ICacheEvents;
use Squanch\Base\Callbacks\ICacheEventsConsumer;
use Squanch\Base\ICachePlugin;
use Squanch\Base\Command\ICmdGet;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Command\ICmdSet;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Plugins\AbstractPlugin;

use Predis\Client;


class PredisPlugin extends AbstractPlugin implements ICachePlugin
{
	/** @var Client */
	private $connector;
	
	
	protected function getCmdGet(): ICmdGet
	{
		return (new Command\Get())->setClient($this->connector);
	}
	
	protected function getCmdHas(): ICmdHas
	{
		return (new Command\Has())->setClient($this->connector);
	}
	
	protected function getCmdDelete(): ICmdDelete
	{
		return (new Command\Delete())->setClient($this->connector);
	}
	
	protected function getCmdSet(): ICmdSet
	{
		return (new Command\Set())->setClient($this->connector);
	}
	
	
	/**
	 * @param Client|array $client Predis client or predis config.
	 */
	public function __construct($client)
	{
		if (is_array($client))
		{
			$this->connector = new Client($client);
		}
		else
		{
			$this->connector = $client;
		}
	}
}