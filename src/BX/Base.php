<?php namespace BX;

class Base
{
	use \BX\Logger\LoggerTrait,
	 \BX\Event\EventTrait,
	 \BX\Translate\TranslateTrait;
	/**
	 * Get package name
	 * @return string
	 */
	protected static function getPackage()
	{
		$path = explode('\\',self::getClassName());
		return $path[0];
	}
	/**
	 * Get server name
	 * @return string
	 */
	protected static function getService()
	{
		$path = explode('\\',self::getClassName());
		return (isset($path[1])) ? $path[1] : false;
	}
	/**
	 * Get class name
	 * @return string
	 */
	protected static function getClassName()
	{
		return get_called_class();
	}
	/**
	 * Get charset
	 * @return string
	 */
	public function getCharset()
	{
		if (Registry::exists('charset')){
			return Registry::get('charset');
		}
		return 'UTF-8';
	}
	/**
	 * Is dev mode
	 * @return boolean
	 */
	protected function isDevMode()
	{
		if (Registry::exists('mode')){
			return Registry::get('mode') === 'dev';
		}
		return false;
	}
}