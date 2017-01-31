<?php
namespace Squanch\Plugins\Squid;


use Squanch\Plugins\Squid\Base\ISquanchSquidConnector;


class SquidGarbageCollector
{
	private $connector;
	
	
	public function __construct(ISquanchSquidConnector $connector)
	{
		$this->connector = $connector;
	}
	
	public function run(\stdClass $params = null)
	{
		if (isset($params->date))
		{
			$date = $params->date;
		}
		else
		{
			$date = new \DateTime();
		}
		
		$this->connector->cmdDelete()
			->where('EndDate < ?', $date)
			->execute();
	}
}