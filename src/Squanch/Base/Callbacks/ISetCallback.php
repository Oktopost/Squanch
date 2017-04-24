<?php
namespace Squanch\Base\Callbacks;


use Squanch\Objects\CallbackData;


interface ISetCallback
{
	public function onSetRequest(bool $result, CallbackData $data);
}