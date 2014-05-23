<?php namespace BX\Mutex;
use BX\Config\DICService;

trait MutexTrait
{
	/**
	 * Get mutex manager
	 * @return IMutexManager
	 */
	public function mutex()
	{
		$name = 'mutex';
		if (DICService::get($name) === null){
			$manager = function(){
				return new MutexManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}