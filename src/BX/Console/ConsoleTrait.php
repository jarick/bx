<?php namespace BX\Console;
use \BX\Console\ConsoleController;
use BX\Base\DI;

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
			DI::set($key,new ConsoleController());
		}
		return DI::get($key);
	}
}