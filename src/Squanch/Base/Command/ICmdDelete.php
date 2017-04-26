<?php
namespace Squanch\Base\Command;


interface ICmdDelete extends ISetupWithConnectorAndCallbacksLoader, IWhere
{
	public function execute(): bool;
}