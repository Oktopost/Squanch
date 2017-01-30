<?php
namespace Squanch\Enum;


use Objection\TEnum;


class InstancePriority
{
	use TEnum;
	
	const LOW    = 10;
	const MEDIUM = 50;
	const HIGH   = 100;
}