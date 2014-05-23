<?php namespace BX\Error;
use BX\Config\DICService;

trait ErrorTrait
{
	protected function error()
	{
		$name = 'error';
		if (DICService::get($name) === null){
			$manager = function(){
				return new ErrorManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}