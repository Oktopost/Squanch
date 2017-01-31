<?php

use Skeleton\Skeleton;
use Skeleton\ConfigLoader\PrefixDirectoryConfigLoader;


class Squanch
{
	/** @var Skeleton */
	private static $skeleton = null;
	
	
	private static function setUp()
	{
		if (self::$skeleton)
			return;
		
		self::$skeleton = new Skeleton();
		self::$skeleton
			->enableKnot()
			->registerGlobalFor('Squanch')
			->useGlobal()
			->setConfigLoader(new PrefixDirectoryConfigLoader('Squanch', __DIR__ . '/../skeleton'));
	}
	
	
	public static function skeleton(string $item = null)
	{
		if (!self::$skeleton)
			self::setUp();
		
		if ($item)
			return self::$skeleton->get($item);
		
		return self::$skeleton;
	}
}