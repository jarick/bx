<?php namespace BX\Base;
use Symfony\Component\Yaml\Yaml;

class Registry
{
	const FORMAT_YAML_FILE = 'yaml_file';
	const FORMAT_YAML = 'yaml';
	const FORMAT_ARRAY = 'array';
	/**
	 * @var string
	 */
	protected static $format;
	/**
	 * @var array
	 */
	protected static $store = [];
	/**
	 * Constructor
	 */
	protected function __construct()
	{

	}
	/**
	 * Clone
	 */
	protected function __clone()
	{

	}
	/**
	 * Load file
	 * @param string $path
	 * @return string
	 * @throws \RuntimeException
	 */
	private static function load($path)
	{
		if (!file_exists($path)){
			throw new \RuntimeException("file `$path` is not found");
		}
		return file_get_contents($path);
	}
	/**
	 * Init store
	 * @param mixed $store
	 * @param string $format
	 * @throws \InvalidArgumentException
	 */
	public static function init($store,$format = null)
	{
		if ($format === null){
			$format = self::FORMAT_YAML_FILE;
		}
		switch ($format){
			case self::FORMAT_YAML_FILE:
				self::$store = Yaml::parse(self::load($store));
				break;
			case self::FORMAT_YAML:
				self::$store = Yaml::parse($store);
				break;
			case self::FORMAT_ARRAY:
				self::$store = $store;
				break;
			default:
				throw new \InvalidArgumentException("Format `$format` is not exists");
		}
	}
	/**
	 * Exists key in store
	 * @return boolean
	 */
	public static function exists()
	{
		$temp = self::$store;
		foreach((array)func_get_args() as $name){
			if (isset($temp[$name])){
				$temp = $temp[$name];
			} else{
				return false;
			}
		}
		return true;
	}
	/**
	 * @param string $name
	 * @return mixed
	 */
	public static function get()
	{
		$temp = self::$store;
		foreach((array)func_get_args() as $name){
			if (isset($temp[$name])){
				$temp = $temp[$name];
			} else{
				throw new \InvalidArgumentException("Key '".$name."' not found in store");
			}
		}
		return $temp;
	}
	/**
	 * Get charset
	 * @return string
	 */
	public static function getCharset()
	{
		if (self::exists('charset')){
			return self::get('charset');
		}
		return 'UTF-8';
	}
	/**
	 * Is dev mode
	 * @return boolean
	 */
	public static function isDevMode()
	{
		if (self::exists('mode')){
			return self::get('mode') === 'dev';
		}
		return false;
	}
}