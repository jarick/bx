<?php namespace BX\Config;
use BX\Config\DICService;

trait ConfigTrait
{
	/**
	 * Return error manager
	 *
	 * @return \BX\Config\IConfigManager
	 */
	protected function config()
	{
		$name = 'config';
		if (DICService::get($name) === null){
			$manager = function(){
				return new ConfigManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}