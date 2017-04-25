<?php
namespace Squanch\Base\Command;


interface ICommand
{
	public function execute(): bool;
}