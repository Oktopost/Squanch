<?php
namespace Squanch\Exceptions;


class OperationNotSupportedOnBucketException extends SquanchException
{
	public function __construct($operationName)
	{
		parent::__construct("The operation '$operationName' on a bucket. Please provide a key");
	}
}