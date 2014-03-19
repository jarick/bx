<?php namespace BX\Console;
use \BX\Console\Manager\ConsoleController;
use BX\DI;

trait ConsoleTrait
{
	/**
	 * get console manager
	 * @return ConsoleController
	 */
	protected function console()
	{
		$key = 'console';
		if (DI::get($key) === null){
			DI::set($key,ConsoleController::getManager());
		}
		return DI::get($key);
	}
}