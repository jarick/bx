<?php namespace BX\Date;
use BX\Config\DICService;

trait DateTrait
{
	/**
	 * @var string
	 */
	private static $manager = 'date';
	/**
	 * Get manager
	 *
	 * @return DateTimeManager
	 */
	protected function date()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new DateTimeManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
}