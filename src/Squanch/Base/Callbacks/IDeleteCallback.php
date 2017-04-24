<?php
namespace Squanch\Base\Callbacks;


use Squanch\Objects\CallbackData;


interface IDeleteCallback
{
	public function onDeleteRequest(bool $result, CallbackData $data);
}