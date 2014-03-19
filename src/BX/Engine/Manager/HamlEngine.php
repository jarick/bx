<?php namespace BX\Engine\Manager;
use BX\Engine\IEngine;
use BX\Manager;
use BX\Registry;
use MtHaml\Environment;
use Symfony\Component\Yaml\Yaml;

class HamlEngine extends Manager implements IEngine
{
	use \BX\FileSystem\FileSystemTrait,
	 \BX\Http\HttpTrait;
	/**
	 * @var string
	 */
	public $suffix_php = '.php';
	/**
	 * @var string
	 */
	public $suffix_haml = '.haml';
	/**
	 * @var string
	 */
	protected $php_cache_folder;
	/**
	 * @var string
	 */
	protected $folder;
	/**
	 * @var Environment
	 */
	protected $haml_engine;
	/**
	 * @var \BX\Engine\Manager\PhpEngine
	 */
	public $php_engine = null;
	/**
	 * @var string
	 */
	public $suffix_yml = '.yml';
	/**
	 * Get real path for file
	 * @param string $path
	 */
	public function getRealPath($path)
	{
		return realpath(str_replace('~',$this->request()->server()->get('DOCUMENT_ROOT'),$path));
	}
	/**
	 * Set path to php file
	 * @param string $php_cache_folder
	 * @return self
	 */
	public function setPhpCacheFolder($php_cache_folder)
	{
		$this->php_cache_folder = $php_cache_folder;
		return $this;
	}
	/**
	 * Get path to php file
	 * @return string
	 */
	protected function getPhpCacheFolder()
	{
		if (isset($this->php_cache_folder)){
			return $this->php_cache_folder;
		} else{
			$path = $this->getRealPath(Registry::get('templating','php'));
			if ($path === false){
				throw new \InvalidArgumentException('Path not found');
			}
			return $this->php_cache_folder = $path;
		}
	}
	/**
	 * Set path to haml file
	 * @param type $folder
	 * @retrun self
	 */
	public function setFolder($folder)
	{
		$this->folder = $folder;
		return $this;
	}
	/**
	 * Get path to haml file
	 * @return string
	 */
	protected function getFolder()
	{
		if (isset($this->folder)){
			return $this->folder;
		} else{
			return $this->folder = $this->getRealPath(Registry::get('templating','haml'));
		}
	}
	/**
	 * Set php engine
	 * @param PhpEngine $engine
	 */
	public function setPhpEngine($engine)
	{
		$this->php_engine = $engine;
		return $this;
	}
	/**
	 * Init
	 */
	public function init()
	{
		if ($this->php_engine === null){
			$this->setPhpEngine(PhpEngine::getManager());
		}
		$this->haml_engine = new Environment('php',['enable_escaper' => false]);
	}
	/**
	 * Render
	 * @param View $view
	 * @param string $path
	 * @param array $params
	 * @return boolean
	 */
	public function render($view,$path,array $params = [])
	{
		$this->fire('HamlRender');
		$php = $this->getPhpCacheFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_php;
		$haml = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_haml;
		$yml = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_yml;
		if (file_exists($yml)){
			$meta = Yaml::parse(file_get_contents($yml));
		} else{
			$meta = [];
		}
		$path_php = false;
		if (file_exists($haml)){
			if (!file_exists($php) || filemtime($php) != filemtime($haml)){
				$this->log()->debug("render haml file `{$haml}`");
				$haml_code = file_get_contents($haml);
				$php_code = $this->haml_engine->compileString($haml_code,$haml);
				$this->filesystem()->checkPathDir($php);
				file_put_contents($php,$php_code);
				$path_php = $path;
				$path_folder = $this->getPhpCacheFolder();
			}
		} else{
			$file = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_php;
			if (file_exists($file)){
				$path_php = $path;
				$path_folder = $this->getFolder();
			}
		}
		if ($path_php){
			$this->php_engine->setFolder($path_folder)->setMeta($meta)->render($view,$path_php,$params);
		} else{
			return false;
		}
		return true;
	}
}