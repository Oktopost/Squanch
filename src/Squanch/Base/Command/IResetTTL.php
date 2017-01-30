<?php
namespace Squanch\Base\Command;


interface IResetTTL
{
	/**
	 * @return static
	 */
	public function resetTTL(int $ttl);
}