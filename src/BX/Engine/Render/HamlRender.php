<?php namespace BX\Engine\Render;
use BX\Engine\Render\IRender;
use MtHaml\Environment;
use Symfony\Component\Yaml\Yaml;

class HamlRender implements IRender
{
	use \BX\Event\EventTrait,
	 \BX\Http\HttpTrait,
	 \BX\Logger\LoggerTrait,
	 \BX\FileSystem\FileSystemTrait,
	 \BX\Config\ConfigTrait;
	/**
	 * @var string
	 */
	public $ext_php = '.php';
	/**
	 * @var string
	 */
	public $suffix_php = '.cache.php';
	/**
	 * @var string
	 */
	public $suffix_haml = '.haml';
	/**
	 * @var string
	 */
	protected $php_cache_folder = null;
	/**
	 * @var string
	 */
	protected $folder = null;
	/**
	 * @var string
	 */
	protected $doc_root = null;
	/**
	 * @var Environment
	 */
	protected $haml_engine;
	/**
	 * @var PhpEngine
	 */
	public $php_engine = null;
	/**
	 * @var string
	 */
	public $suffix_yml = '.yml';
	/**
	 * @var string
	 */
	public $suffix_less = '.less';
	/**
	 * @var string
	 */
	public $suffix_coffee = '.coffee';
	/**
	 * @var string
	 */
	public $suffix_css = '.css';
	/**
	 * @var string
	 */
	public $folder_css = 'css';
	/**
	 * @var string
	 */
	public $suffix_js = '.js';
	/**
	 * @var string
	 */
	public $folder_js = 'js';
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
		if ($this->php_cache_folder === null){
			$this->php_cache_folder = $this->getRealPath($this->config()->get('templating','php'));
		}
		return $this->php_cache_folder;
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
	 * Get path to document root
	 * @return string
	 */
	protected function getDocRootFolder()
	{
		if ($this->doc_root === null){
			$this->doc_root = $this->getRealPath($this->config()->get('templating','doc_root'));
		}
		return $this->doc_root;
	}
	/**
	 * Get path to haml file
	 * @return string
	 */
	protected function getFolder()
	{
		if ($this->folder === null){
			$this->folder = $this->getRealPath($this->config()->get('templating','haml'));
		}
		return $this->folder;
	}
	/**
	 * Set php engine
	 *
	 * @param PhpEngine $engine
	 */
	public function setPhpEngine($engine)
	{
		$this->php_engine = $engine;
		return $this;
	}
	/**
	 * Constructor
	 */
	public function __construct($php_engine = null)
	{
		if ($php_engine === null){
			$this->setPhpEngine(new PhpRender());
		}else{
			$this->setPhpEngine($php_engine);
		}
		$this->haml_engine = new Environment('php',[
			'enable_escaper' => false,
			'escape_attrs'	 => false,
			'escape_html'	 => false,
		]);
	}
	/**
	 * Render jss
	 *
	 * @param string $path
	 */
	private function renderScript($view,$path)
	{
		$js = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_js;
		if (file_exists($js)){
			$js_new = $this->getDocRootFolder().DIRECTORY_SEPARATOR.$this->folder_js.DIRECTORY_SEPARATOR.$path.$this->suffix_js;
			if (!file_exists($js_new) || filemtime($js) !== filemtime($js_new)){
				$this->log('engine.render.haml')->debug("copy js file `{$js}`");
				$js_code = file_get_contents($js);
				$this->filesystem()->checkPathDir(dirname($js_new));
				file_put_contents($js_new,$js_code);
				touch($js);
			}
			$list = (isset($view['footer_js'])) ? $view['footer_js'] : [];
			$list[] = DIRECTORY_SEPARATOR.$this->folder_js.DIRECTORY_SEPARATOR.$path.$this->suffix_js;
			$view['footer_js'] = array_unique($list);
		}
	}
	/**
	 * Render less
	 *
	 * @param string $path
	 */
	private function renderStyle($view,$path)
	{
		$less = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_less;
		if (file_exists($less)){
			$css = $this->getDocRootFolder().DIRECTORY_SEPARATOR.$this->folder_css.DIRECTORY_SEPARATOR.$path.$this->suffix_css;
			if (!file_exists($css) || filemtime($less) !== filemtime($css)){
				$this->log('engine.render.haml')->debug("render less file `{$less}`");
				$less_code = file_get_contents($less);
				$lessc = new \lessc();
				$css_code = $lessc->compile($less_code);
				$this->filesystem()->checkPathDir(dirname($css));
				file_put_contents($css,$css_code);
				touch($css);
			}
			$list = (isset($view['css'])) ? $view['css'] : [];
			$list[] = DIRECTORY_SEPARATOR.$this->folder_css.DIRECTORY_SEPARATOR.$path.$this->suffix_css;
			$view['css'] = array_unique($list);
		}
	}
	/**
	 * Is exists page
	 * @param string $path
	 * @return boolean
	 */
	public function exists($path)
	{
		$haml = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_haml;
		if (file_exists($haml)){
			return true;
		}else{
			$file = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_php;
			if (file_exists($file)){
				return true;
			}
		}
		return false;
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
		$this->fire('HamlEngineRender',[$view,$path,$params]);
		$php = $this->getPhpCacheFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_php;
		$haml = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_haml;
		$yml = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->suffix_yml;
		if (file_exists($yml)){
			$meta = Yaml::parse(file_get_contents($yml));
		}else{
			$meta = [];
		}
		$path_php = false;
		$engine = $this->php_engine;
		if (file_exists($haml)){
			if (!file_exists($php) || filemtime($php) != filemtime($haml)){
				$this->log()->debug("render haml file `{$haml}`");
				$haml_code = file_get_contents($haml);
				$php_code = $this->haml_engine->compileString($haml_code,$haml);
				$this->filesystem()->checkPathDir(dirname($php));
				file_put_contents($php,$php_code);
				touch($haml);
			}
			$path_php = $path;
			$engine->setFolder($this->getPhpCacheFolder())->setSuffixPhp($this->suffix_php);
		}else{
			$file = $this->getFolder().DIRECTORY_SEPARATOR.$path.$this->ext_php;
			if (file_exists($file)){
				$path_php = $path;
				$engine->setFolder($this->getFolder())->setSuffixPhp($this->ext_php);
			}
		}
		if ($path_php){
			$this->renderStyle($view,$path);
			$this->renderScript($view,$path);
			$this->php_engine->setMeta($meta)->render($view,$path_php,$params);
		}else{
			return false;
		}
		return true;
	}
}