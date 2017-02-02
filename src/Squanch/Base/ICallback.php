<?php
namespace Squanch\Base;


use Squanch\Objects\CallbackData;


interface ICallback
{
	public function fire(CallbackData $data);
}