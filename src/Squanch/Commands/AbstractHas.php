<?php
namespace Squanch\Commands;


use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Base\Command\ICmdHas;
use Squanch\Objects\CallbackData;
use Squanch\Callbacks\CallbacksHandler;


abstract class AbstractHas implements ICmdHas
{
	use \Squanch\Commands\Traits\TResetTTL;
	use \Squanch\Commands\Traits\TWhere;

	
	private $connector;
	
	/** @var CallbacksHandler */
	private $callbacksHandler;
	
	
	protected function getConnector()
	{
		return $this->connector;
	}
	
	
	protected abstract function onCheck(CallbackData $data): bool;
	protected abstract function onUpdateTTL(CallbackData $data, int $ttl);
	
	
	/**
	 * @return static
	 */
	public function setup($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksHandler = new CallbacksHandler($callbacksLoader);
		return $this;
	}
	

	public function check(): bool
	{
		if (!$this->key())
			throw new \Exception('A key must be provided for the has command');
		
		$result = $this->onCheck($this->dataObject());
		
		if ($result && $this->hasTTL())
		{
			$this->onUpdateTTL($this->dataObject(), $this->getTTL());
		}
		
		$this->callbacksHandler->onHasRequest($result, $this->dataObject());
		
		return $result;
	}
}