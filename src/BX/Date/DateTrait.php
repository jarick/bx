<?php
namespace BX\Date;
use BX\Registry;
use BX\Date\Manager\DateTimeManager;

trait DateTrait
{
	private function getDateTime()
	{
		static $object;
		if(!isset($object)){
			$object = DateTimeManager::getManager();
		}
		return $object;
	}
	
	/**
	 * @return DateTimeManager
	 **/
	public function date()
	{
		return $this->getDateTime();
	}
}