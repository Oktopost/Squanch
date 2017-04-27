<?php
namespace Squanch\Plugins\Squid\Utils;


use Squanch\Enum\TTL;
use Squanch\Objects\CallbackData;
use Squid\MySql\Impl\Connectors\MySqlObjectConnector;


class UpdateTTL
{
	/** @var MySqlObjectConnector */
	private $conn;
	
	
	public function __construct(MySqlObjectConnector $conn)
	{
		$this->conn = $conn;
	}


	public function updateTTL(CallbackData $data, int $ttl)
	{
		$endDate = ($ttl >= 0 ? 
			(new \DateTime())->modify("+ $ttl seconds")->format('Y-m-d H:i:s') : 
			TTL::END_OF_TIME);
		
		$this->conn->updateByFields(
			[
				'TTL' 		=> $ttl, 
				'EndDate'	=> $endDate
			],
			[
				'Id'		=> $data->Key,
				'Bucket'	=> $data->Bucket
			]);
	}
}