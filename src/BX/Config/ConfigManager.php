<?php namespace BX\Config;
use Symfony\Component\Yaml\Yaml;

class ConfigManager implements IConfigManager
{
	const FORMAT_YAML_FILE = 'yaml_file';
	const FORMAT_YAML = 'yaml';
	const FORMAT_ARRAY = 'array';
	const FORMAT_ARRAY_FILE = 'array_file';
	/**
	 * @var string
	 */
	protected $format;
	/**
	 * @var array
	 */
	protected $store = [];
	/**
	 * Load file
	 *
	 * @param string $path
	 * @return string
	 * @throws \RuntimeException
	 */
	private function load($path)
	{
		if (!file_exists($path)){
			throw new \RuntimeException("file `$path` is not found");
		}
		return file_get_contents($path);
	}
	/**
	 * Init store
	 *
	 * @param string $format
	 * @param mixed $store
	 * @throws \InvalidArgumentException
	 */
	public function init($format,$store)
	{
		switch ($format){
			case self::FORMAT_YAML_FILE:
				$this->store = Yaml::parse($this->load($store));
				break;
			case self::FORMAT_YAML:
				$this->store = Yaml::parse($store);
				break;
			case self::FORMAT_ARRAY:
				$this->store = $store;
				break;
			case self::FORMAT_ARRAY_FILE:
				$this->store = include($store);
				break;
			default:
				throw new \InvalidArgumentException("Format `$format` is not exists");
		}
		return true;
	}
	/**
	 * Exists key in store
	 *
	 * @return boolean
	 */
	public function exists()
	{
		$temp = $this->store;
		$args = func_get_args();
		if (is_array($args[0])){
			$args = $args[0];
		}
		foreach($args as $name){
			if (array_key_exists($name,$temp)){
				$temp = $temp[$name];
			}else{
				return false;
			}
		}
		return true;
	}
	/**
	 * Return value by key
	 *
	 * @return string
	 */
	public function get()
	{
		$temp = $this->store;
		$args = func_get_args();
		if (is_array($args[0])){
			$args = $args[0];
		}
		foreach($args as $name){
			if (array_key_exists($name,$temp)){
				$temp = $temp[$name];
			}else{
				throw new \InvalidArgumentException("Key '".$name."' not found in store");
			}
		}
		return $temp;
	}
	/**
	 * Get all store
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->store;
	}
	/**
	 * Get charset
	 *
	 * @return string
	 */
	public function getCharset()
	{
		if ($this->exists('charset')){
			return $this->get('charset');
		}
		return 'UTF-8';
	}
	/**
	 * Is dev mode
	 *
	 * @return boolean
	 */
	public function isDevMode()
	{
		if ($this->exists('mode')){
			return $this->get('mode') === 'dev';
		}
		return false;
	}
}