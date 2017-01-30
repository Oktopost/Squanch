<?php
namespace Squanch\Enum;


use Objection\TEnum;


class InstanceType
{
	use TEnum;
	
	const HARD	= 'hard';
	const SOFT	= 'soft';
}