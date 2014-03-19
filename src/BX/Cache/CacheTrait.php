<?php namespace BX\Cache;
use BX\Cache\Manager\Cache;
use BX\DI;

trait CacheTrait
{
	/**
	 * get cache manager
	 * @return Cache
	 */
	protected function cache()
	{
		$key = 'cache';
		if (DI::get($key) === null){
			DI::set($key,Cache::getManager());
		}
		return DI::get($key);
	}
}