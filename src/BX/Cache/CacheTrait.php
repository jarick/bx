<?php namespace BX\Cache;
use BX\Cache\CacheManager;
use BX\Base\DI;

trait CacheTrait
{
	/**
	 * get cache manager
	 * @return CacheManager
	 */
	protected function cache()
	{
		$key = 'cache';
		if (DI::get($key) === null){
			DI::set($key,new CacheManager());
		}
		return DI::get($key);
	}
}