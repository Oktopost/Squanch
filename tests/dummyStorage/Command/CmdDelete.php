<?php
namespace dummyStorage\Command;


use Squanch\Enum\Events;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractDelete;

use dummyStorage\DummyConnector;


class CmdDelete extends AbstractDelete implements ICmdDelete
{
	/** @var DummyConnector */
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
		$result = false;
		$bucket = $this->getBucket();
		$key = $bucket . $this->getKey();
		
		$db = $this->connector->getDb();
		
		if (isset($db[$key]) && $db[$key]->EndDate > (new \DateTime()) && $db[$key]->Bucket == $bucket)
		{
			unset($db[$key]);
			$this->connector->setDb($db);
			$result = true;
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_DELETE, ['key' => $key, 'bucket' => $bucket]);
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_DELETE, ['key' => $key, 'bucket' => $bucket,]);
		}
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_DELETE, [
			'key' => $key, 'bucket' => $bucket, 'event' => $result ? Events::SUCCESS : Events::FAIL]);
		
		$this->reset();
		
		return $result;
	}
}