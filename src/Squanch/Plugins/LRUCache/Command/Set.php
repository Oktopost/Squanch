<?php
namespace Squanch\Plugins\LRUCache\Command;


use Squanch\Commands\AbstractSet;
use Squanch\Objects\Data;
use Squanch\Plugins\LRUCache\Base\ILRUAdapter;


class Set extends AbstractSet
{
	/** @var ILRUAdapter */
	private $lruAdapter;
	
	
	protected function onInsert(Data $data): bool
	{
		return $this->lruAdapter->createItem($data);
	}

	protected function onUpdate(Data $data): bool
	{
		return $this->lruAdapter->updateItem($data);
	}

	protected function onSave(Data $data): bool
	{
		return $this->lruAdapter->setItem($data);
	}

	
	public function __construct(ILRUAdapter $lruAdapter)
	{
		parent::__construct();
		
		$this->lruAdapter = $lruAdapter;
	}
}