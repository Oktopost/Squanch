<?php
namespace Squanch\Objects;


use Objection\LiteSetup;
use Objection\LiteObject;



/**
 * @property string $Key
 * @property string $Bucket
 * @property Data $Data
 */
class CallbackData extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Key'    => LiteSetup::createString(''),
			'Bucket' => LiteSetup::createString(''),
			'Data'   => LiteSetup::createInstanceOf(Data::class)
		];
	}
	
	
	public function __construct(string $bucket = '', string $key = '')
	{
		parent::__construct();
		$this->Bucket = $bucket;
		$this->Key = $key;
	}


	public function hasData(): bool
	{
		return is_null($this->Data);
	}
	
	
	public function setKey(string $key)
	{
		$this->Key = $key;
		return $this;
	}
	
	public function setBucket(string $bucket)
	{
		$this->Bucket = $bucket;
		return $this;
	}
	
	public function setData(Data $data)
	{
		$this->Data = $data;
		return $this;
	}
}