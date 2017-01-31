<?php
namespace Squanch\Plugins\Squid\Base;


use Squid\MySql\Command\ICmdDelete;


interface ISquanchSquidConnector
{
	/**
	 * @return bool
	 */
	public function deleteByFields(array $fields);
	
	public function cmdDelete(): ICmdDelete;
}