<?php namespace BX\String;
use BX\String\StringManager;
use BX\Config\DICService;

trait StringTrait
{
	/**
	 * Get string manager
	 *
	 * @return StringManager
	 */
	protected function string()
	{
		$name = 'string';
		if (DICService::get($name) === null){
			$manager = function(){
				return new StringManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}