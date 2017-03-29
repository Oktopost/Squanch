<?php
namespace Squanch\Plugins\Predis\Command;


use Predis\Client;
use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdDelete;
use Squanch\AbstractCommand\AbstractDelete;

use Squanch\Objects\CallbackData;
use Squanch\Exceptions\SquanchException;


class Delete extends AbstractDelete implements ICmdDelete
{
	protected function getConnector(): Client
	{
		return parent::getConnector();
	}
	
	
	public function execute(): bool
	{
		$callbackData = new CallbackData();
		
		if ($this->getKey())
		{
			$callbackData->setKey($this->getKey());
		}
		
		if ($this->getBucket())
		{
			$callbackData->setBucket($this->getBucket());
		}
		
		if (!$this->getKey() && !$this->getBucket())
		{
			throw new SquanchException('Fields not set');
		}
		
		$result = false;
		
		if ($this->getKey() && !$this->getBucket())
		{
			$result = $this->getConnector()->del([$this->getKey()]);
		}
		else if ($this->getKey() && $this->getBucket())
		{
			$result = $this->getConnector()->hdel($this->getBucket(), $this->getKey());
		}
		else if (!$this->getKey() && $this->getBucket())
		{
			$result = $this->getConnector()->del([$this->getBucket()]);
		}
		
		if ($result)
		{
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