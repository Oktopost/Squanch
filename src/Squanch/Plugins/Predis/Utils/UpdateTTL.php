<?php
namespace Squanch\Plugins\Predis\Utils;


use Squanch\Enum\TTL;
use Squanch\Objects\CallbackData;
use Predis\Client;


class UpdateTTL
{
	/** @var Client */
	private $client;
	
	
	private function getFullKey(CallbackData $data)
	{
		return "{$data->Bucket}:{$data->Key}";
	}
	
	
	public function __construct(Client $client)
	{
		$this->client = $client;
	}


	public function updateTTL(CallbackData $data, int $ttl)
	{
		$key = $this->getFullKey($data);
		$endDate = ($ttl >= 0 ? 
			(new \DateTime())->modify("+ $ttl seconds")->format('Y-m-d H:i:s') : 
			TTL::END_OF_TIME);
		
		if ($ttl < 0)
		{
			$this->client->persist($key);
		}
		else
		{
			$this->client->expire($key, $ttl);
		}
		
		$this->client->hset($key, 'TTL', $ttl);
		$this->client->hset($key, 'EndDate', $endDate);
	}
}