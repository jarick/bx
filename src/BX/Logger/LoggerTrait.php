<?php namespace BX\Logger;
use BX\Logger\Manager\LoggerManager;
use BX\DI;

trait LoggerTrait
{
	/**
	 * @return LoggerManager
	 * */
	public function log()
	{
		if (DI::get('logger') === null){
			DI::set('logger',LoggerManager::getManager());
		}
		return DI::get('logger');
	}
}