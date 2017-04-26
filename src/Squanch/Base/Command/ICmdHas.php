<?php
namespace Squanch\Base\Command;


use Squanch\Base\Callbacks\Provider\IHasEventProvider;


interface ICmdHas extends IHasEventProvider, IWhere, IResetTTL
{
	public function check(): bool;
}