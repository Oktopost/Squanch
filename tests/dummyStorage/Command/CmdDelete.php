<?php
namespace dummyStorage\Command;


use Squanch\Enum\Events;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\IByKey;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractDelete;

use dummyStorage\DummyConnector;


class CmdDelete extends AbstractDelete implements ICmdDelete
{
	/** @var DummyConnector */
	private $connector;
	private $key;
	
	private $callbacksLoader;
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}

	
	public function __construct($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
	}
	
	public function byKey(string $key): IByKey
	{
		$this->key = $key;
		
		return $this;
	}
	
	public function execute(): bool
	{
		$result = false;
		$key = $this->key;
		unset($this->key);
		
		$db = $this->connector->getDb();
		
		if (isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()))
		{
			unset($db[$key]);
			$this->connector->setDb($db);
			$result = true;
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_DELETE, ['key' => $key]);
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_DELETE, ['key' => $key]);
		}
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_DELETE, [
			'key' => $key, 'event' => $result ? Events::SUCCESS : Events::FAIL]);
		
		return $result;
	}
}