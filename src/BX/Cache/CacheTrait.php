<?php namespace BX\Cache;
use BX\Config\DICService;

trait CacheTrait
{
	/**
	 * Return error manager
	 *
	 * @return ICacheManger
	 */
	protected function cache()
	{
		$name = 'cache';
		if (DICService::get($name) === null){
			$manager = function(){
				return new CacheManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}