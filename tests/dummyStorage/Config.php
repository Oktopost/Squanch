<?php
namespace dummyStorage;


use Squanch;
use Squanch\Enum\InstanceType;
use Squanch\Enum\InstancePriority;
use Squanch\Base\ICachePlugin;
use Squanch\Base\Boot\IBoot;
use Squanch\Base\Boot\IConfigLoader;
use Squanch\Objects\Instance;
use Squanch\Plugins\Squid\SquidPlugin;
use Squanch\Plugins\PhpCache\PhpCachePlugin;

use Squid\MySql;
use Predis\Client;
use Cache\Adapter\Predis\PredisCachePool;


class Config
{
	/** @var ICachePlugin */
	private $plugin;
	
	/** @var ICachePlugin[] */
	private $plugins;
	
	
	private function initRedisInstance()
	{
		$client = new Client([
			'scheme' => 'tcp',
			'host' => '127.0.0.1',
			'port' => 6379
		]);
		
		$plugin = new PhpCachePlugin(new PredisCachePool($client));
		
		$instance = new Instance();
		$instance->Name = 'redis';
		$instance->Type = InstanceType::SOFT;
		$instance->Plugin = $plugin;
		
		return $instance;
	}
	
	private function initPredisInstance()
	{
		$instance = new Client(array(
			"scheme" => "tcp",
			"host" => "127.0.0.1",
			"port" => 6379));
		
		$plugin = new Squanch\Plugins\Predis\PredisPlugin($instance);
		$instance = new Instance();
		$instance->Name = 'predis';
		$instance->Type = InstanceType::SOFT;
		$instance->Plugin = $plugin;
		
		return $instance;
	}
	
	private function initSquidInstance($tableName = 'HardCache', $instanceName = 'squid')
	{
		$mysql = new MySql();
		
		$mysql
			->config()
			->addConfig(
				'main', [
				'host' => 'localhost',
				'port' => 3306,
				'user' => 'root',
				'password' => '',
				'database' => 'squanch_cache'
			]);
		
		
		$plugin = new SquidPlugin($mysql->getConnector('main'), $tableName);
		
		$instance = new Instance();
		$instance->Name = $instanceName;
		$instance->Type = InstanceType::HARD;
		$instance->Plugin = $plugin;
		
		return $instance;
	}
	
	private function initDummyInstance()
	{
		$instance = new Instance();
		$instance->Name = 'dummy';
		$instance->Type = InstanceType::SOFT;
		$instance->Priority = InstancePriority::MEDIUM;
		$instance->Plugin = new DummyStoragePlugin();
		
		return $instance;
	}
	
	public function __construct()
	{
		$instanceName = getenv('instance');
	
		if (!$instanceName)
		{
			$instanceName = 'dummy';
		}
		
		$event = new Squanch\Events\Handler();

		/** @var IConfigLoader $configLoader */
		$configLoader = Squanch::skeleton(IConfigLoader::class);
		
		$configLoader->addInstance($this->initDummyInstance());
		$configLoader->addInstance($this->initSquidInstance('HardCache'));
		$configLoader->addInstance($this->initSquidInstance('SoftCache', 'squidSoft'));
		$configLoader->addInstance($this->initRedisInstance());
		$configLoader->addInstance($this->initPredisInstance());
		
		foreach ($configLoader->getInstances() as $instance)
		{
			$instance->Plugin->setEventManager($event);
		}
		
		/** @var IBoot $squanch */
		$squanch = Squanch::skeleton(IBoot::class);
		$squanch->setConfigLoader($configLoader);
		
		$this->plugins = [
			'dummy' => $squanch->resetFilters()->filterInstancesByName('dummy')->getPlugin(),
			'squid' => $squanch->resetFilters()->filterInstancesByName('squid')->getPlugin(),
			'squidSoft' => $squanch->resetFilters()->filterInstancesByName('squidSoft')->getPlugin(),
		    'redis' => $squanch->resetFilters()->filterInstancesByName('redis')->getPlugin(),
		    'predis' => $squanch->resetFilters()->filterInstancesByName('predis')->getPlugin()
		];
		
		$this->plugin = $squanch->resetFilters()->filterInstancesByName($instanceName)->getPlugin();
	}
	
	/**
	 * @return ICachePlugin;
	 */
	public function getPlugin()
	{
		return $this->plugin;
	}
	
	/**
	 * @return ICachePlugin[]
	 */
	public function getAllPlugins()
	{
		return $this->plugins;
	}
}