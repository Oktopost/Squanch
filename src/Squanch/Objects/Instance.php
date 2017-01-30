<?php
namespace Squanch\Objects;


use Squanch\Base\IPlugin;
use Squanch\Enum\InstanceType;
use Squanch\Enum\InstancePriority;

use Objection\LiteObject;
use Objection\LiteSetup;


/**
 * @property string $Name
 * @property string $Type
 * @property int $Priority
 * @property IPlugin $Plugin,
 */
class Instance extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Name'     => LiteSetup::createString(),
			'Type'     => LiteSetup::createEnum(InstanceType::class),
			'Priority' => LiteSetup::createInt(InstancePriority::MEDIUM),
			'Plugin'   => LiteSetup::createInstanceOf(IPlugin::class),
		];
	}
}