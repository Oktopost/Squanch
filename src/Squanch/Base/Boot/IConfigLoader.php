<?php
namespace Squanch\Base\Boot;


use Squanch\Objects\Instance;


interface IConfigLoader
{
	public function addInstance(Instance $instance, bool $override = false): IConfigLoader;

	/**
	 * @return Instance[]
	 */
	public function getInstances(): array;
}