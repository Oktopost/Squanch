<?php
namespace Squanch\Base\Boot;

use Squanch;
use Squanch\Boot\Boot;
use Squanch\Boot\ConfigLoader;
use Squanch\Boot\CallbacksLoader;


Squanch::skeleton()->set(IBoot::class, Boot::class);
Squanch::skeleton()->set(IConfigLoader::class, ConfigLoader::class);
Squanch::skeleton()->set(ICallbacksLoader::class, CallbacksLoader::class);