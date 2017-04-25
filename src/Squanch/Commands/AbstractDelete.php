<?php
namespace Squanch\Commands;


use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Callbacks\CallbacksHandler;


abstract class AbstractDelete implements ICmdDelete
{
	use \Squanch\Commands\Helpers\TWhere;
	
	
	private $connector;
	
	/** @var CallbacksHandler */
	private $callbacksHandler;
	
	
	protected function getConnector()
	{
		return $this->connector;
	}
	
	
	protected abstract function onDeleteBucket(string $bucket): bool;
	protected abstract function onDeleteItem(string $bucket, string $key): bool;
	
	
	/**
	 * @return static
	 */
	public function setup($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksHandler = new CallbacksHandler($callbacksLoader);
		return $this;
	}
	
	public function execute(): bool
	{
		if ($this->key())
		{
			$result = $this->onDeleteItem($this->bucket(), $this->key());
		}
		else
		{
			$result = $this->onDeleteBucket($this->bucket());
		}
		
		$this->callbacksHandler->onDeleteRequest($result, $this->dataObject());
		
		return $result;
	}
}