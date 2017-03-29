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
	
	
	private function afterExecute()
	{
		$this->data = [];
	}
	
	
	public function __construct(array $data)
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

		$this->afterExecute();
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
		
		$this->afterExecute();
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
		
		$this->afterExecute();
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
		
		$this->afterExecute();
		return $result;
	}
	
	/**
	 * @return Data[]|bool
	 */
	public function asArrayOfData()
	{
		$result = $this->data;
		$this->afterExecute();
		
		return $result;
	}
}