<?php namespace BX\Base;

/**
 * @deprecated
 */
class DI
{
	/**
	 * @var array
	 */
	private static $store = [];
	/**
	 * Get object by name
	 * @param string $name
	 * @return mixed
	 */
	public static function get($name)
	{
		if (isset(self::$store[$name])){
			return self::$store[$name];
		}
		return null;
	}
	/**
	 * Set object
	 * @param string $name
	 * @param myxed $object
	 */
	public static function set($name,$object)
	{
		self::$store[$name] = $object;
	}
}