<?php namespace BX\Logger;
use BX\Config\DICService;

trait LoggerTrait
{
	/**
	 * Get logger manager
	 *
	 * @param string $name
	 * @return \Monolog\Logger
	 */
	public function log($name = 'default')
	{
		$service = 'logger';
		if (DICService::get($service) === null){
			$manager = function(){
				return new LoggerManager();
			};
			DICService::set($service,$manager);
		}
		return DICService::get($service)->get($name);
	}
}