<?php
namespace Squanch\Base\Callbacks;


use Squanch\Objects\CallbackData;


interface IHasCallback
{
	public function onHasRequest(bool $result, CallbackData $data);
}