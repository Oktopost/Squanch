<?php
namespace Squanch;


use Skeleton\Skeleton;
use Skeleton\Base\ISkeletonInit;
use Skeleton\ConfigLoader\PrefixDirectoryConfigLoader;


class SkeletonInit implements ISkeletonInit
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
			->setConfigLoader(new PrefixDirectoryConfigLoader('Squanch', __DIR__ . '/../skeleton'));
	}
	
	
	public static function skeleton(?string $item = null)
	{
		if (!self::$skeleton)
			self::setUp();
		
		if ($item)
			return self::$skeleton->get($item);
		
		return self::$skeleton;
	}
}