<?php namespace BX\Counter;
use BX\Config\DICService;

trait CounterTrait
{
	/**
	 * Get counter manager
	 *
	 * @return CounterManager
	 */
	protected function getManager()
	{
		$name = 'counter';
		if (DICService::get($name) === null){
			$manager = function(){
				return new CounterManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}