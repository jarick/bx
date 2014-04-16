<?php namespace BX\ZendSearch;
use BX\Base\DI;
use BX\ZendSearch\ZendSearchManager;

trait ZendSearchTrait
{
	/**
	 * Get search manager
	 * @return ZendSearchManager
	 */
	public function zendsearch()
	{
		$key = 'zend_search';
		if (DI::get($key) === null){
			DI::set($key,new ZendSearchManager());
		}
		return DI::get($key);
	}
}