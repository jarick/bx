<?php namespace BX\Logger;
use BX\Logger\LoggerManager;
use BX\Base\DI;

trait LoggerTrait
{
	/**
	 * Get logger manager
	 * @param string $name
	 * @return \Monolog\Logger
	 */
	public function log($name = 'default')
	{
		if (DI::get('logger') === null){
			DI::set('logger',new LoggerManager());
		}
		return DI::get('logger')->get($name = 'default');
	}
}