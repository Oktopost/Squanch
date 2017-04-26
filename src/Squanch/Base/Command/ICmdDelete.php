<?php
namespace Squanch\Base\Command;


use Squanch\Base\Callbacks\Provider\IDeleteEventProvider;


interface ICmdDelete extends IWhere, IDeleteEventProvider
{
	public function execute(): bool;
}