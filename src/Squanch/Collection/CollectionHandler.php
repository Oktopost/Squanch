<?php
namespace Squanch\Collection;


use Objection\LiteObject;
use Objection\Mapper;
use Squanch\Base\Command\IGetCollection;
use Squanch\Objects\Data;


class CollectionHandler implements IGetCollection
{
	/** @var Data[] */
	private $data;
	
	
	public function __construct(array $data = [])
	{
		$this->data = $data;
	}
	
	
	/**
	 * @return array|bool
	 */
	public function asArrays()
	{
		$result = [];
		
		foreach ($this->data as $data)
		{
			$item = json_decode($data->Value, true);
			
			if (is_array($item))
			{
				$result[] = $item;
			}
		}
		
		return $result;
	}
	
	/**
	 * @return \stdClass[]|bool
	 */
	public function asObjects()
	{
		$result = [];
		
		foreach ($this->data as $data)
		{
			$item = json_decode($data->Value);
			
			if (is_object($item))
			{
				$result[] = $item;
			}
		}
		
		return $result;
	}
	
	/**
	 * @return LiteObject[]|bool
	 */
	public function asLiteObjects(string $liteObjectName)
	{
		$mapper = Mapper::createFor($liteObjectName);
		$result = [];
		
		foreach ($this->data as $data)
		{
			$result[] = $mapper->getObject($data->Value, $liteObjectName);
		}
		
		return $result;
	}
	
	/**
	 * @return string[]|bool
	 */
	public function asStrings()
	{
		$result = [];
		
		foreach ($this->data as $data)
		{
			$result[] = $data->Value;
		}
		
		return $result;
	}
}