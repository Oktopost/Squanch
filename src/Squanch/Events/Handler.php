<?php
namespace Squanch\Events;


use Squanch\Base\Callbacks\Events;
use Squanch\Base\Callbacks\Consumer;
use Squanch\Base\Callbacks\ICacheEvents;
use Squanch\Events\CommandEvents;


class Handler implements ICacheEvents
{
	/** @var CommandEvents\DeleteEventHandler */
	private $delete;
	
	/** @var CommandEvents\SetEventHandler  */
	private $set;
	
	/** @var CommandEvents\GetEventHandler */
	private $get;
	
	/** @var CommandEvents\HasEventHandler */
	private $has;
	
	
	public function __construct()
	{
		$this->delete	= new CommandEvents\DeleteEventHandler();
		$this->set		= new CommandEvents\SetEventHandler();
		$this->get		= new CommandEvents\GetEventHandler();
		$this->has		= new CommandEvents\HasEventHandler();
	}
	
	public function __clone()
	{
		$this->delete	= clone $this->delete;
		$this->set		= clone $this->set;
		$this->get		= clone $this->get;
		$this->has		= clone $this->has;
	}


	public function onHas(): Consumer\IOnHas
	{
		return $this->has;
	}

	public function onGet(): Consumer\IOnGet
	{
		return $this->get;
	}

	public function onSet(): Consumer\IOnSet
	{
		return $this->set;
	}

	public function onDelete(): Consumer\IOnDelete
	{
		return $this->delete;
	}
	

	public function hasEvent(): Events\IHasEvent
	{
		return $this->has;
	}

	public function getEvent(): Events\IGetEvent
	{
		return $this->get;
	}

	public function setEvent(): Events\ISetEvent
	{
		return $this->set;
	}

	public function deleteEvent(): Events\IDeleteEvent
	{
		return $this->delete;
	}
}