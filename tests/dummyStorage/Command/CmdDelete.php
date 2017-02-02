<?php
namespace dummyStorage\Command;


use dummyStorage\DummyConnector;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdDelete;
use Squanch\AbstractCommand\AbstractDelete;

use Squanch\Objects\CallbackData;


class CmdDelete extends AbstractDelete implements ICmdDelete
{
	protected function getConnector(): DummyConnector
	{
		return parent::getConnector();
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
		
		$db = $this->getConnector()->getDb();
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
			$this->getConnector()->setDb($db);
			$this->getCallbacksLoader()->executeCallback(Callbacks::SUCCESS_ON_DELETE, $callbackData);
		}
		else
		{
			$this->getCallbacksLoader()->executeCallback(Callbacks::FAIL_ON_DELETE, $callbackData);
		}
		
		$this->getCallbacksLoader()->executeCallback(Callbacks::ON_DELETE, $callbackData);
		
		$this->reset();
		
		return $result;
	}
}