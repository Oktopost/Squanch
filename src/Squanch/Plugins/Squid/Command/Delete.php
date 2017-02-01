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
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	
	public function __construct($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
	}
	
	
	public function execute(): bool
	{
		$result = $this->connector->deleteByFields(['Id' => $this->getKey(), 'Bucket' => $this->getBucket()]);
		
		if ($result)
		{
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_DELETE, [
				'key' => $this->getKey(),
				'bucket' => $this->getBucket()
			]);
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_DELETE, [
				'key' => $this->getKey(),
				'bucket' => $this->getBucket()
			]);
		}
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_DELETE, [
			'key' => $this->getKey(),
			'bucket' => $this->getBucket(),
			'event' => ($result ? Events::SUCCESS : Events::FAIL)
		]);
		
		$this->reset();
		
		return $result;
	}
}