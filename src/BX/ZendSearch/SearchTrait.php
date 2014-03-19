<?php namespace BX\ZendSearch;
use BX\ZendSearch\Manager\ZendSearchManager;

trait SearchTrait
{
	/**
	 * Get search manager
	 * @return ZendSearchManager
	 */
	public function zendsearch()
	{
		$key = 'zend_search';
		if (DI::get($key) === null){
			DI::set($key,ZendSearchManager::getManager());
		}
		return DI::get($key);
	}
}