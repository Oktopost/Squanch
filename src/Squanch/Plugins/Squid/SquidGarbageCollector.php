<?php
namespace Squanch\Plugins\Squid;


use Squid\MySql\IMySqlConnector;


class SquidGarbageCollector
{
	/** @var IMySqlConnector */
	private $connector;
	private $tableName;
	
	private $date;
	private $sleepMS			= 10;
	private $limitPerIteration	= 100;
	private $maxIterations    	= 999;
	
	
	private function delete(): int
	{
		return $this->connector
			->delete()
			->from($this->tableName)
			->where('EndDate < ?', date('c', $this->date))
			->limitBy($this->limitPerIteration)
			->executeDml(true);
	}

	
	public function __construct(IMySqlConnector $connector, $tableName)
	{
		$this->connector = $connector;
		$this->tableName = $tableName;
		$this->date = time();
	}
	
	
	public function setMaxIterations(int $maxIterations)
	{
		$this->maxIterations = $maxIterations;
		return $this;
	}
	
	public function setLimitPerIteration(int $limit)
	{
		$this->limitPerIteration = $limit;
		return $this;
	}
	
	public function setEndDate(int $date)
	{
		$this->date = $date;
		return $this;
	}
	
	public function setWaitTime(int $ms)
	{
		$this->sleepMS = $ms;
		return $this;
	}
	
	
	public function run(): int
	{
		$totalDeleted = 0;
		
		for ($i = 0; $i < $this->maxIterations; $i++)
		{
			$deleted = $this->delete();
			
			if ($deleted == 0)
				break;
			
			$totalDeleted += $deleted;
			usleep($this->sleepMS * 1000);
		}
		
		return $totalDeleted;
	}
}