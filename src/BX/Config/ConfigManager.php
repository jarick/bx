<?php namespace BX\Config;
use Symfony\Component\Yaml\Yaml;

class ConfigManager implements IConfigManager
{
	const FORMAT_YAML_FILE = 'yaml_file';
	const FORMAT_YAML = 'yaml';
	const FORMAT_ARRAY = 'array';
	/**
	 * @var string
	 */
	protected $format;
	/**
	 * @var array
	 */
	protected $store = array();
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
	 * @param mixed $store
	 * @param string $format
	 * @throws \InvalidArgumentException
	 */
	public function init($store,$format = null)
	{
		if ($format === null){
			$format = self::FORMAT_YAML_FILE;
		}
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
			default:
				throw new \InvalidArgumentException("Format `$format` is not exists");
		}
		return true;
	}
	/**
	 * Exists key in store
	 *
	 * @param array $key
	 * @return boolean
	 */
	public function exists(array $key)
	{
		$temp = $this->store;
		foreach($key as $name){
			if (isset($temp[$name])){
				$temp = $temp[$name];
			}else{
				return false;
			}
		}
		return true;
	}
	/**
	 * @param string $name
	 *
	 * @param array $key
	 * @return mixed
	 */
	public function get(array $key)
	{
		$temp = $this->store;
		foreach($key as $name){
			if (isset($temp[$name])){
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
}