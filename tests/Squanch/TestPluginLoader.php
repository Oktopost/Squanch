<?php
namespace Squanch;


use Squanch\Base\ICachePlugin;
use Squanch\Plugins\Squid\SquidPlugin;
use Squanch\Plugins\Predis\PredisPlugin;
use Squanch\Plugins\Fallback\FallbackPlugin;
use Squanch\Plugins\InMemoryCache\InMemoryPlugin;

use Predis\Client;


class TestPluginLoader
{
	private static function getSquid(): ICachePlugin
	{
		return new SquidPlugin([
			'user'	=> 'root',
			'pass'	=> '',
			'db'	=> 'squanch_cache'
		], 'HardCache');
	}
	
	private static function getPredis(): ICachePlugin
	{
		return new PredisPlugin(new Client());
	}
	
	private static function getInMemory(): ICachePlugin
	{
		return new InMemoryPlugin();
	}
	
	private static function getFallback(): ICachePlugin
	{
		return new FallbackPlugin([
			new InMemoryPlugin(),
			new InMemoryPlugin()
		]);
	}
	
	private static function getComplexFallback(): ICachePlugin
	{
		return new FallbackPlugin([
			self::getInMemory(),
			self::getPredis(),
			self::getSquid()
		]);
	}
	
	
	public static function get(): ICachePlugin
	{	
		switch (getenv('CACHE_PLUGIN_TYPE'))
		{
			case 'squid':
				return self::getSquid();
				
			case 'predis':
				return self::getPredis();
				
			case 'inmemory':
				return self::getInMemory();
				
			case 'fallback':
				return self::getFallback();
				
			case 'fallback-advanced':
				return self::getComplexFallback();
				
			default:
				throw new \Exception('Set CACHE_PLUGIN_TYPE to run sanity tests');
		}
	}
}