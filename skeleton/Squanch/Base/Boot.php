<?php
namespace Squanch\Base\Boot;
/** @var \Skeleton\Base\IBoneConstructor $this */


use Squanch;
use Squanch\Boot\Boot;
use Squanch\Boot\ConfigLoader;
use Squanch\Boot\CallbacksLoader;


$this->set(IBoot::class,			Boot::class);
$this->set(IConfigLoader::class,	ConfigLoader::class);
$this->set(ICallbacksLoader::class,	CallbacksLoader::class);