<?php
namespace Squanch\Base\Callbacks;


use Squanch\Objects\CallbackData;


interface IGetCallback
{
	public function onGetRequest(bool $result, CallbackData $data);
}