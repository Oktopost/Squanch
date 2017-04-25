<?php
namespace Squanch\Objects;


use Squanch\Enum\TTL;
use Squanch\Enum\Bucket;

use Objection\Mapper;
use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string		$Id
 * @property string		$Bucket
 * @property string		$Value
 * @property int		$TTL
 * @property \DateTime	$EndDate
 * @property \DateTime	$Created
 * @property \DateTime	$Modified
 */
class Data extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Id'       => LiteSetup::createString(),
			'Bucket'   => LiteSetup::createString(Bucket::DEFAULT_BUCKET_NAME),
			'Value'    => LiteSetup::createString(),
			'TTL'      => LiteSetup::createInt(TTL::DEFAULT_TTL),
			'EndDate'  => LiteSetup::createDateTime(),
			'Created'  => LiteSetup::createDateTime(),
			'Modified' => LiteSetup::createDateTime()
		];
	}
	
	
	public function setTTL($newTTL)
	{
		if (!is_int($newTTL))
		{
			$newTTL = TTL::DEFAULT_TTL;
		}
		
		if ($newTTL < 0)
		{
			$interval = TTL::FOREVER;
		}
		else
		{
			$interval = $newTTL;
		}
		
		$this->TTL = $newTTL;
		$this->EndDate = (new \DateTime())->modify("+ {$interval} seconds");
	}
	
	
	public function serialize(): \stdClass
	{
		$mapper = Mapper::createFor(Data::class);
		return $mapper->getStdClass($this);
	}
	
	public static function deserialize(\stdClass $data): Data
	{
		$mapper = Mapper::createFor(Data::class);
		
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $mapper->getObject($data);
	}
}