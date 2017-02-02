<?php
namespace dummyStorage\Command;


use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdDelete;
use Squanch\Base\Boot\ICallbacksLoader;
use Squanch\AbstractCommand\AbstractDelete;

use dummyStorage\DummyConnector;
use Squanch\Objects\CallbackData;


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
		$callbackData = new CallbackData();
		
		$result = false;
		$bucket = $this->getBucket();
		$key = $bucket;
		
		if ($this->getKey())
		{
			$key .= $this->getKey();
			$callbackData->setKey($this->getKey());
		}
		
		if ($this->getBucket())
		{
			$callbackData->setBucket($this->getBucket());
		}
		
		$db = $this->connector->getDb();
		$total = 0;
		
		foreach ($db as $id => $value)
		{
			if (preg_match('/^'.$key.'/', $id) && $value->EndDate > (new \DateTime()) && $value->Bucket == $bucket)
			{
				$result = true;
				unset($db[$id]);
				$total++;
			}
		}
		
		if ($result)
		{
			unset($db[$key]);
			$this->connector->setDb($db);
			$this->callbacksLoader->executeCallback(Callbacks::SUCCESS_ON_DELETE, $callbackData);
		}
		else
		{
			$this->callbacksLoader->executeCallback(Callbacks::FAIL_ON_DELETE, $callbackData);
		}
		
		$this->callbacksLoader->executeCallback(Callbacks::ON_DELETE, $callbackData);
		
		$this->reset();
		
		return $result;
	}
}