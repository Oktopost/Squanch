<?php
namespace Squanch\Base\Command;


use Squanch\Objects\Data;
use Objection\LiteObject;


interface IGetCollection
{
	/**
	 * @return array|bool
	 */
	public function asArrays();
	
	/**
	 * @return \stdClass[]|bool
	 */
	public function asObjects();
	
	/**
	 * @return LiteObject[]|bool
	 */
	public function asLiteObjects(string $liteObjectName);
	
	/**
	 * @return string[]|bool
	 */
	public function asStrings();
	
	/**
	 * @return Data[]|bool
	 */
	public function asArrayOfData();
}