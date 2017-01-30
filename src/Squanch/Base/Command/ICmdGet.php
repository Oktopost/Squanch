<?php
namespace Squanch\Base\Command;


use Squanch\Objects\Data;

use Objection\LiteObject;


interface ICmdGet extends IConstructWithConnectorAndCallbacksLoader, ICommand, IByKey, IResetTTL
{
	/**
	 * @return array|bool
	 */
	public function asArray();
	
	/**
	 * @return \stdClass|bool
	 */
	public function asObject();
	
	/**
	 * @return LiteObject|bool
	 */
	public function asLiteObject(string $liteObjectName);
	
	/**
	 * @return string|bool
	 */
	public function asString();
	
	/**
	 * @return int|float|bool
	 */
	public function asNumber();
	
	/**
	 * @return Data|bool
	 */
	public function asData();
}