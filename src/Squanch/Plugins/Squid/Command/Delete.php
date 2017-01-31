<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Enum\Events;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractDelete;

use Squid\MySql\Connectors\IMySqlObjectConnector;


class Delete extends AbstractDelete implements ICmdDelete
{
	/** @var IMySqlObjectConnector */
	private $connector;
	
	/** @var ICallbacksLoader  */
	private $callbacksLoader;
	
	private $key;
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	
	public function __construct($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
	}
	
	
	/**
	 * @return static
	 */
	public function byKey(string $key)
	{
		$this->key = $key;
		return $this;
	}
	
	public function execute(): bool
	{
		$result = $this->connector->deleteByFields(['Id' => $this->key]);
		
		if ($result)
		{
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_DELETE, ['key' => $this->key]);
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_DELETE, ['key' => $this->key]);
		}
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_DELETE, [
			'key' => $this->key, 
			'event' => ($result ? Events::SUCCESS : Events::FAIL)
		]);
		
		unset($this->key);
		
		return $result;
	}
}