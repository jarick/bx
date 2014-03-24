<?php namespace BX\Engine\Manager;
use BX\Manager;
use BX\Engine\IEngine;
use Symfony\Component\Yaml\Yaml;

class PhpEngine extends Manager implements IEngine, \ArrayAccess
{
	/**
	 * @var string
	 */
	public $suffix_php = '.php';
	/**
	 * @var string
	 */
	public $folder;
	/**
	 * @var \BX\MVC\Manager\View
	 */
	public $view;
	/**
	 * @var array
	 */
	private $meta = [];
	/**
	 * @var string
	 */
	public $suffix_yml = '.yml';
	/**
	 * Set folder
	 * @param string $folder
	 * @return \BX\Engine\Manager\PhpEngine
	 */
	public function setFolder($folder)
	{
		$this->folder = $folder;
		return $this;
	}
	/**
	 * Set meta
	 * @param array $meta
	 */
	public function setMeta(array $meta = [])
	{
		$this->meta = $meta;
		return $this;
	}
	/**
	 * Render file
	 * @param type $view
	 * @param type $path
	 * @param type $params
	 * @return boolean
	 */
	public function render($view,$path,array $params = [])
	{
		$this->view = $view;
		$this->fire('PhpEngineRender');
		$php = $this->folder.DIRECTORY_SEPARATOR.$path.$this->suffix_php;
		if (!file_exists($php)){
			return false;
		}
		$yml = $this->folder.DIRECTORY_SEPARATOR.$path.$this->suffix_yml;
		if (file_exists($yml)){
			$this->meta = Yaml::parse(file_get_contents($yml));
		}
		if (is_array($params)){
			extract($params,EXTR_PREFIX_SAME,'data');
		}
		$this->log()->debug("render php file `{$php}`");
		if (file_exists($php)){
			require($php);
			return true;
		} else{
			return false;
		}
	}
	/**
	 * Is set meta
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->meta[$offset]);
	}
	/**
	 * Get meta
	 * @param string $offset
	 * @return null|string
	 */
	public function offsetGet($offset)
	{
		return isset($this->meta[$offset]) ? $this->meta[$offset] : null;
	}
	/**
	 * Set meta
	 * @param string $offset
	 * @param string $value
	 */
	public function offsetSet($offset,$value)
	{
		$this->meta[$offset] = $value;
	}
	/**
	 * Unset meta
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->meta[$offset]);
	}
}