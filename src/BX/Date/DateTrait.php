<?php namespace BX\Date;
use BX\Base\DI;
use BX\Date\DateTimeManager;

trait DateTrait
{
	/**
	 * @return DateTimeManager
	 * */
	public function date()
	{
		if (DI::get('date') === null){
			DI::set('date',new DateTimeManager());
		}
		return DI::get('date');
	}
}