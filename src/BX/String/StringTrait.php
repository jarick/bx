<?php namespace BX\String;
use BX\String\Manager\StringManager;

trait StringTrait
{
	/**
	 * Get string manager
	 * @return StringManager
	 */
	public function string()
	{
		static $oManager;
		if ( ! isset($oManager)){
			$oManager = StringManager::getManager();
		}
		return $oManager;
	}
}
