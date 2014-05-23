<?php namespace BX\ZendSearch;
use BX\Config\DICService;

trait ZendSearchTrait
{
	/**
	 * Get search manager
	 *
	 * @return ZendSearchManager
	 */
	public function zendsearch()
	{
		$name = 'zend_search';
		if (DICService::get($name) === null){
			$manager = function(){
				return new ZendSearchManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}