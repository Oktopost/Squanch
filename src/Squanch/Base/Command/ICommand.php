<?php
namespace Squanch\Base\Command;


interface ICommand extends ICmdCallback
{
	public function execute(): bool;
}