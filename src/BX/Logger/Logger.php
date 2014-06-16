<?php namespace BX\Logger;
use BX\Logger\LoggerManager;
use BX\Config\DICService;

class Logger
{
	/**
	 * @var string
	 */
	private static $manager = 'logger';
	/**
	 * Get manager
	 *
	 * @return LoggerManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new LoggerManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Return instance of monolog logger
	 *
	 * @param string $name
	 * @return \Monolog\Logger
	 */
	public static function getInstance($name = 'default')
	{
		return self::getManager()->get($name);
	}
}