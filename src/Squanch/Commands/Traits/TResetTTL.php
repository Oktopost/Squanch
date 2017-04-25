<?php
namespace Squanch\Commands\Traits;


trait TResetTTL
{
	/** @var int|null */
	private $_resetTTL = null;
	
	
	/**
	 * @return int|null
	 */
	protected function getTTL() 
	{
		return $this->_resetTTL;
	}
	
	protected function hasTTL(): bool
	{
		return !is_null($this->_resetTTL);
	}
	
	
	/**
	 * @param int $ttl
	 * @return static
	 */
	public function resetTTL(int $ttl)
	{
		$this->_resetTTL = $ttl;
		return $this;
	}
}