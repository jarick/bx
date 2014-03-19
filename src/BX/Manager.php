<?php namespace BX;

abstract class Manager extends Object
{
	public function init()
	{

	}
	/**
	 * Get regex class
	 * @return string
	 */
	static protected function getRegexClass()
	{
		return "/^(\w+)\\\\(\w+)\\\\Manager\\\\(\w+)/";
	}
	/**
	 * Get class
	 * @param string $sPackage
	 * @param string $sService
	 * @param string $sManager
	 * @return string
	 */
	static protected function getClass($sPackage,$sService,$sManager)
	{
		return $sPackage."\\".$sService."\\Manager\\".ucwords($sManager);
	}
	/**
	 * Get maanger
	 * @param string $sManager
	 * @param array $aParams
	 * @return self
	 */
	public static function getManager($sManager = false,$aParams = [])
	{
		$instance = static::autoload($sManager,'managers',$aParams);
		$instance->init();
		return $instance;
	}
}