<?php
namespace Squanch\Base\Command;


interface ICmdHas extends ISetupWithConnectorAndCallbacksLoader, IWhere, IResetTTL
{
	public function check(): bool;
}