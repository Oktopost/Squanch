<?php
namespace Squanch\Base\Boot;
/** @var \Skeleton\Base\IBoneConstructor $this */


use Squanch\Boot\Boot;
use Squanch\Boot\ConfigLoader;


$this->set(IBoot::class,			Boot::class);
$this->set(IConfigLoader::class,	ConfigLoader::class);