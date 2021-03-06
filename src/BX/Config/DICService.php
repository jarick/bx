<?php namespace BX\Config;

class DICService
{
	/**
	 * @var \Pimple
	 */
	static $container = null;
	/**
	 * Return Pimple container
	 *
	 * @return \Pimple
	 */
	public static function getContainer()
	{
		if (self::$container == null){
			self::$container = new \Pimple();
		}
		return self::$container;
	}
	/**
	 * Return save manager by key
	 *
	 * @param string $key
	 * @return null
	 */
	public static function get($key)
	{
		$container = self::getContainer();
		if (!isset($container[$key])){
			return null;
		}
		return $container[$key];
	}
	/**
	 * Set manager
	 *
	 * @param string $key
	 * @param \Closure $value
	 */
	public static function set($key,\Closure $value)
	{
		$container = self::getContainer();
		$container[$key] = $value;
		self::$container = $container;
	}
	/**
	 * Unset value by key
	 *
	 * @param string $key
	 * @return boolean
	 */
	public static function delete($key)
	{
		$container = self::getContainer();
		unset($container[$key]);
		return true;
	}
	/**
	 * Update manager
	 *
	 * @param string $key
	 * @param \Closure $value
	 */
	public static function update($key,\Closure $value)
	{
		self::delete($key);
		self::set($key,$value);
	}
}