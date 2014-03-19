<?php namespace BX;
use Symfony\Component\Yaml\Yaml;

class Registry
{
	const FORMAT_YAML_FILE = 'yaml_file';
	const FORMAT_YAML = 'yaml';
	const FORMAT_ARRAY = 'array';
	protected static $format;
	protected static $store = array();
	protected function __construct()
	{

	}
	protected function __clone()
	{

	}
	private static function load($path)
	{
		if (!file_exists($path)){
			throw new \RuntimeException("file `$path` is not found");
		}
		return file_get_contents($path);
	}
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
	public static function exists()
	{
		$temp = self::$store;
		foreach ((array) func_get_args() as $name){
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
		foreach ((array) func_get_args() as $name){
			if (isset($temp[$name])){
				$temp = $temp[$name];
			} else{
				throw new \InvalidArgumentException("Key '".$name."' not found in store");
			}
		}
		return $temp;
	}
}
?>