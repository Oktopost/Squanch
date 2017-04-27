<?php
namespace Squanch\Enum;


use Objection\TEnum;


class TTL
{
	use TEnum;
	
	const TEN_MINUTES  = 10 * 60;
	const ONE_HOUR     = self::TEN_MINUTES * 6;
	const TWELVE_HOURS = self::ONE_HOUR * 12;
	const ONE_DAY      = self::TWELVE_HOURS * 2;
	const ONE_WEEK     = self::ONE_DAY * 7;
	const ONE_YEAR     = self::ONE_DAY * 365;
	
	const FOREVER      = self::ONE_YEAR * 100;
	const DEFAULT_TTL  = self::ONE_HOUR;
	
	
	const END_OF_TIME	= '3999-12-31 23:59:59';
}