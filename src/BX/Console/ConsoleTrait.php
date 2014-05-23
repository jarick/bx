<?php namespace BX\Console;
use \BX\Console\ConsoleController;
use BX\Config\DICService;

trait ConsoleTrait
{
	/**
	 * get console manager
	 * @return ConsoleController
	 */
	protected function console()
	{
		$name = 'console';
		if (DICService::get($name) === null){
			$manager = function(){
				return new ConsoleController();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}