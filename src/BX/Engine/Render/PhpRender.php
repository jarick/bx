<?php namespace BX\Engine\Render;
use BX\Engine\Render\IRender;
use BX\Base\Registry;
use Symfony\Component\Yaml\Yaml;

class PhpRender implements IRender, \ArrayAccess
{
	use \BX\Event\EventTrait,
	 \BX\Http\HttpTrait,
	 \BX\Logger\LoggerTrait;
	/**
	 * @var string
	 */
	public $suffix_php = '.php';
	/**
	 * @var string
	 */
	public $folder = null;
	/**
	 * @var \BX\MVC\View
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
	 * Set suffix php
	 * @param string $suffix_php
	 * @return \BX\Engine\Render\PhpRender
	 */
	public function setSuffixPhp($suffix_php)
	{
		$this->suffix_php = $suffix_php;
		return $this;
	}
	/**
	 * Get real path for file
	 * @param string $path
	 */
	public function getRealPath($path)
	{
		$path = realpath(str_replace('~',$this->request()->server()->get('DOCUMENT_ROOT'),$path));
		if ($path === false){
			$this->view->throwPageNotFound();
		}
		return $path;
	}
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
	 * Get folder
	 * @return string
	 */
	public function getFolder()
	{
		if ($this->folder === null){
			$this->folder = $this->getRealPath(Registry::get('templating','php'));
		}
		return $this->folder;
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
	 * Is exists page
	 * @param string $path
	 * @return boolean
	 */
	public function exists($path)
	{
		$php = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_php;
		if (file_exists($php)){
			return true;
		}
		return false;
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
		$this->fire('PhpEngineRender',[$view,$path,$params]);
		$php = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_php;
		if (!file_exists($php)){
			return false;
		}
		$yml = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_yml;
		if (file_exists($yml)){
			$this->meta = Yaml::parse(file_get_contents($yml));
		}
		if (is_array($params)){
			extract($params,EXTR_PREFIX_SAME,'data');
		}
		$this->log('engine.render.php')->debug("render php file `{$php}`");
		if (file_exists($php)){
			require($php);
			return true;
		}else{
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