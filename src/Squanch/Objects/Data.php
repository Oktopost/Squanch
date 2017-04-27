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
		
		$this->TTL = $newTTL;
		
		if ($newTTL < 0)
		{
			$this->EndDate = new \DateTime(TTL::END_OF_TIME);
		}
		else
		{
			$this->EndDate = (new \DateTime())->modify("+ {$newTTL} seconds");
		}
	}
	
	
	public function serialize(): string 
	{
		$mapper = Mapper::createFor(Data::class);
		return $mapper->getJson($this);
	}
	
	public function serializeToArray(): array 
	{
		$this->setTTL($this->TTL);
		
		return [
			'Id'       => $this->Id,
			'Bucket'   => $this->Bucket,
			'Value'    => $this->Value,
			'TTL'      => (string)$this->TTL,
			'EndDate'  => $this->EndDate->format('Y-m-d H:i:s'),
			'Created'  => $this->Created->format('Y-m-d H:i:s'),
			'Modified' => $this->Modified->format('Y-m-d H:i:s')
		];
	}

	/**
	 * @param array|\stdClass|string $data
	 * @return Data
	 */
	public static function deserialize($data): Data
	{
		$mapper = Mapper::createFor(Data::class);
		
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $mapper->getObject($data);
	}
}