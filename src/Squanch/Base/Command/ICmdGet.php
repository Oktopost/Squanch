<?php
namespace Squanch\Base\Command;


use Squanch\Objects\Data;

use Objection\LiteObject;


interface ICmdGet extends ISetupWithConnectorAndCallbacksLoader, ICommand, IWhere, IResetTTL, ICmdGetCallback
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
	 * @return LiteObject[]|bool
	 */
	public function asArrayOfLiteObjects(string $liteObjectName);
	
	/**
	 * @return string|bool
	 */
	public function asString();
	
	/**
	 * @return Data|bool
	 */
	public function asData();
	
	/**
	 * @return IGetCollection
	 */
	public function asCollection($limit = 999);
}