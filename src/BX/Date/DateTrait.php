<?php namespace BX\Date;
use BX\DI;
use BX\Date\Manager\DateTimeManager;

trait DateTrait
{
	/**
	 * @return DateTimeManager
	 * */
	public function date()
	{
		if (DI::get('date') === null){
			DI::set('date',DateTimeManager::getManager());
		}
		return DI::get('date');
	}
}