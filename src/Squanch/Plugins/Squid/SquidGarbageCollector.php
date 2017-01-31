<?php
namespace Squanch\Plugins\Squid;


use Squanch\Plugins\Squid\Base\ISquanchSquidConnector;


class SquidGarbageCollector
{
	private $connector;
	
	private $date;
	private $maxIterations     = 999;
	private $limitPerIteration = 100;
	
	
	private function delete(): int
	{
		return $this->connector->cmdDelete()
			->limitBy($this->limitPerIteration)
			->where('EndDate < ?', $this->date)
			->executeDml(true);
	}

	
	public function __construct(ISquanchSquidConnector $connector)
	{
		$this->connector = $connector;
		$this->date = new \DateTime();
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
	
	public function setEndDate(\DateTime $date)
	{
		$this->date = $date;
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
			time_nanosleep(0, 100);
		}
		
		return $totalDeleted;
	}
}