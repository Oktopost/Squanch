<?php
namespace Squanch\Enum;


use Objection\TEnum;


class Bucket
{
	use TEnum;
	
	const DEFAULT_BUCKET_NAME = 'default';
}