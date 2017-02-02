<?php
namespace Squanch\Plugins\Squid\Command;


use Squanch\Enum\Callbacks;
use Squanch\Base\Command\ICmdDelete;
use Squanch\AbstractCommand\AbstractDelete;

use Squanch\Objects\CallbackData;
use Squanch\Exceptions\SquanchException;

use Squid\MySql\Connectors\IMySqlObjectConnector;


class Delete extends AbstractDelete implements ICmdDelete
{
	protected function getConnector(): IMySqlObjectConnector
	{
		return parent::getConnector();
	}
	
	
	public function execute(): bool
	{
		$fields = [];
		$callbackData = new CallbackData();
		
		if ($this->getKey())
		{
			$fields['Id'] = $this->getKey();
			$callbackData->setKey($this->getKey());
		}
		
		if ($this->getBucket())
		{
			$fields['Bucket'] = $this->getBucket();
			$callbackData->setBucket($this->getBucket());
		}
		
		if (!$fields)
		{
			throw new SquanchException('Fields not set');
		}
		
		$result = $this->getConnector()->deleteByFields($fields);
		
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