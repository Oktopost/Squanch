<?php
namespace dummyStorage;


use Squanch;
use Squanch\Base\Boot\IBoot;
use Squanch\Base\Boot\IConfigLoader;
use Squanch\Base\ICachePlugin;
use Squanch\Enum\InstancePriority;
use Squanch\Enum\InstanceType;
use Squanch\Objects\Data;
use Squanch\Objects\Instance;
use Squanch\Plugins\PhpCache\PhpCachePlugin;
use Squanch\Plugins\Squid\SquanchSquidConnector;
use Squanch\Plugins\Squid\SquidPlugin;

use Squid\MySql;
use Predis\Client;
use Cache\Adapter\Predis\PredisCachePool;


require_once __DIR__ . '/../../vendor/autoload.php';


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
	
	private function initMigrationInstance()
	{
		$instanceA = $this->initSquidInstance('HardCache');
		$instanceB = $this->initSquidInstance('SoftCache', 'squid_soft');
		
		$plugin = new Squanch\Decorators\Migration\MigrationDecorator($instanceA->Plugin, $instanceB->Plugin);
		
		$instance = new Instance();
		$instance->Name = 'migration';
		$instance->Type = InstanceType::HARD;
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
		
		
		$connector = new SquanchSquidConnector();
		
		$connector
			->setConnector($mysql->getConnector())
			->setDomain(Data::class)
			->setTable($tableName)
			->setIgnoreFields(['Created', 'Modified']);
		
		
		$plugin = new SquidPlugin($connector);
		
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

		/** @var IConfigLoader $configLoader */
		$configLoader = Squanch::skeleton(IConfigLoader::class);
		
		$configLoader->addInstance($this->initDummyInstance());
		$configLoader->addInstance($this->initSquidInstance('HardCache'));
		$configLoader->addInstance($this->initSquidInstance('SoftCache', 'squidSoft'));
		$configLoader->addInstance($this->initMigrationInstance());
		$configLoader->addInstance($this->initRedisInstance());
		
		/** @var IBoot $squanch */
		$squanch = Squanch::skeleton(IBoot::class);
		$squanch->setConfigLoader($configLoader);
		
		$this->plugins = [
			'dummy' => $squanch->resetFilters()->filterInstancesByName('dummy')->getPlugin(),
			'squid' => $squanch->resetFilters()->filterInstancesByName('squid')->getPlugin(),
			'squidSoft' => $squanch->resetFilters()->filterInstancesByName('squidSoft')->getPlugin(),
			'migration' => $squanch->resetFilters()->filterInstancesByName('migration')->getPlugin(),
		    'redis' => $squanch->resetFilters()->filterInstancesByName('redis')->getPlugin(),
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