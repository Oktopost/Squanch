<?php
namespace Squanch;


use Squanch\Base\ICachePlugin;


trait TSanityTest
{
	/** @var ICachePlugin */
	private $cache;
	
	
	protected function loadPlugin()
	{
		$this->cache = TestPluginLoader::get();
	}
}