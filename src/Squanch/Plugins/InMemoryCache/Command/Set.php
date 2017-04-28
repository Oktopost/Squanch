<?php
namespace Squanch\Plugins\InMemoryCache\Command;


use Squanch\Commands\AbstractSet;
use Squanch\Objects\Data;
use Squanch\Plugins\InMemoryCache\Base\IStorage;


class Set extends AbstractSet
{
	/** @var IStorage */
	private $storage;
	
	
	public function __construct(IStorage $storage)
	{
		parent::__construct();
		$this->storage = $storage;
	}
	
	
	protected function onInsert(Data $data): bool
	{
		if ($this->storage->hasKey($data->Bucket, $data->Id))
			return false;
		
		return $this->storage->setItem($data);
	}

	protected function onUpdate(Data $data): bool
	{
		if (!$this->storage->hasKey($data->Bucket, $data->Id))
			return false;
		
		return $this->storage->setItem($data);
	}

	protected function onSave(Data $data): bool
	{
		return $this->storage->setItem($data);
	}
}