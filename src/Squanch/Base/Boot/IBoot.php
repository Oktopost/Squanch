<?php
namespace Squanch\Base\Boot;


use Squanch\Base\ICachePlugin;


interface IBoot
{
	public function setConfigLoader(IConfigLoader $configLoader): IBoot;
	
	/**
	 * @return static
	 */
	public function resetFilters();
	
	public function filterInstancesByType(string $type): IBoot;
	
	public function filterInstancesByName(string $name): IBoot;
	
	public function filterInstancesByPriorityLessOrEqual(int $priority): IBoot;
	
	public function filterInstancesByPriorityGreaterOrEqual(int $priority): IBoot;
	
	public function filterInstancesByPriority(int $priority): IBoot;
	
	public function getPlugin(): ICachePlugin;
}