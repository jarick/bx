<?php namespace BX\String;
use BX\String\StringManager;
use BX\Base\DI;

trait StringTrait
{
	/**
	 * Get string manager
	 * @return StringManager
	 */
	public function string()
	{
		$key = 'string';
		if (DI::get($key) === null){
			DI::set($key,new StringManager());
		}
		return DI::get($key);
	}
}
