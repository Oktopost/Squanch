<?php
namespace Squanch\Base\Command;


use Squanch\Base\Callbacks\Events\IGetEvent;
use Squanch\Base\Callbacks\Provider\IGetEventProvider;
use Squanch\Objects\Data;
use Objection\LiteObject;


interface ICmdGet extends IWhere, IResetTTL, IGetEventProvider
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
	public function asLiteObjects(string $class);
	
	/**
	 * @return string|bool
	 */
	public function asString();
	
	/**
	 * @return Data|bool
	 */
	public function asData();
}