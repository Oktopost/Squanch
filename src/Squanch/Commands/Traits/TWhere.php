<?php
namespace Squanch\Commands\Helpers;


use Squanch\Objects\CallbackData;


trait TWhere
{
	/** @var CallbackData|null */
	private $_dataObject = null;
	
	
	private function _createDataObjectIfNotExists()
	{
		if (!$this->_dataObject)
			$this->_dataObject = new CallbackData();
	}
	
	
	public function dataObject(): CallbackData
	{
		$this->_createDataObjectIfNotExists();
		return $this->dataObject();
	}
	
	public function key(): string
	{
		$this->_createDataObjectIfNotExists();
		return $this->_dataObject->Key;
	}
	
	public function bucket(): string
	{
		$this->_createDataObjectIfNotExists();
		return $this->_dataObject->Bucket;
	}


	/**
	 * @return static
	 */
	public function byKey(string $key)
	{
		$this->_createDataObjectIfNotExists();
		$this->_dataObject->Key = $key;
		return $this;
	}

	/**
	 * @return static
	 */
	public function byBucket(string $bucket)
	{
		$this->_createDataObjectIfNotExists();
		$this->_dataObject->Bucket = $bucket;
		return $this;
	}
}