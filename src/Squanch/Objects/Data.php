<?php
namespace Squanch\Objects;


use Objection\LiteObject;
use Objection\LiteSetup;


/**
 * @property string $Id
 * @property string $Value
 * @property \DateTime $EndDate
 * @property \DateTime $Created
 * @property \DateTime $Modified
 * @property int $TTL
 */
class Data extends LiteObject
{
	const FOREVER_IN_SEC     = 60 * 60 * 24 * 365 * 100;
	const DEFAULT_TTL_IS_SEC = 60 * 60;
	
	
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Id'       => LiteSetup::createString(),
			'Value'    => LiteSetup::createString(),
			'EndDate'  => LiteSetup::createDateTime(),
			'Created'  => LiteSetup::createDateTime(),
			'Modified' => LiteSetup::createDateTime(),
			'TTL'      => LiteSetup::createInt(self::DEFAULT_TTL_IS_SEC)
		];
	}
	
	
	public function setTTL($newTTL)
	{
		if (!is_int($newTTL))
		{
			$newTTL = self::DEFAULT_TTL_IS_SEC;
		}
		
		if ($newTTL < 0)
		{
			$interval = self::FOREVER_IN_SEC;
		}
		else if ($newTTL == 0)
		{
			$interval = 0;
		}
		else
		{
			$interval = $newTTL;
		}
		
		$this->TTL = $newTTL;
		$this->EndDate = (new \DateTime())->modify("+ {$interval} seconds");
	}
}