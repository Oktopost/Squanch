<?php
namespace Squanch\Base\Command;


use Squanch\Objects\Data;
use Objection\LiteObject;


interface ICmdGet extends ISetupWithConnectorAndCallbacksLoader, IWhere, IResetTTL
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
	public function asLiteObject(string $class);
	
	/**
	 * @return LiteObject[]|bool
	 */
	public function asArrayOfLiteObjects(string $class);
	
	/**
	 * @return string|bool
	 */
	public function asString();
	
	/**
	 * @return Data|bool
	 */
	public function asData();
}