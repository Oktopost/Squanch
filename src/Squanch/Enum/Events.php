<?php
namespace Squanch\Enum;


use Objection\TEnum;


class Events
{
	use TEnum;
	
	const SUCCESS = 'success';
	const FAIL    = 'fail';
}