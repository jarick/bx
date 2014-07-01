<?php namespace BX\MVC;
use BX\Base\Collection;
use BX\Config\DICService;
use BX\Http\Response;
use BX\MVC\Entity\SiteEntity;
use BX\MVC\Exception\RenderException;
use InvalidArgumentException;

class SiteController
{
	use \BX\String\StringTrait,
	 \BX\Event\EventTrait,
	 \BX\Http\HttpTrait,
	 \BX\Logger\LoggerTrait,
	 \BX\Config\ConfigTrait;
	/**
	 * @var string
	 */
	protected $site_folder = 'sites';
	/**
	 * @var string
	 */
	protected $layout_folder = 'layouts';
	/**
	 * @var string
	 */
	protected $main_template_file = 'template';
	/**
	 * @var string
	 */
	protected $layout = false;
	/**
	 * @var string
	 */
	protected $site_name = false;
	/**
	 * @var Collection
	 */
	private $site;
	/**
	 * Return view
	 *
	 * @return View
	 */
	public function view()
	{
		$key = 'view';
		if (DICService::get($key) === null){
			$manager = function(){
				return new View();
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
	/**
	 * Return render exception
	 *
	 * @return RenderException
	 */
	private function getRenderException()
	{
		$key = 'render_exception';
		if (DICService::get($key) === null){
			$manager = function(){
				return new RenderException();
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
	/**
	 * Return site folder
	 *
	 * @return string
	 */
	public function getSiteFolder()
	{
		return $this->site_folder;
	}
	/**
	 * Return site name
	 *
	 * @return string
	 */
	public function getSiteName()
	{
		return $this->site_name;
	}
	/**
	 * Set site name
	 *
	 * @param string $site
	 * @return string
	 */
	public function setSiteName($site)
	{
		$this->fire('set site name',[&$site]);
		$this->log('mvc.site')->debug("set site name `$site`");
		$this->view()->loadMeta('sites',$site);
		return $this->site_name = $site;
	}
	/**
	 * Return layout name
	 *
	 * @return string
	 */
	public function getLayout()
	{
		return $this->layout;
	}
	/**
	 * Set layout
	 *
	 * @param string $layout
	 */
	public function setLayout($layout)
	{
		$this->fire('SetLayout',[&$layout]);
		$this->layout = $layout;
	}
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->site = new Collection(SiteEntity::getClass());
		if ($this->config()->exists('sites')){
			foreach($this->config()->get('sites') as $site){
				$entity = new SiteEntity();
				$entity->setData($site);
				$this->addSite($entity);
			}
		}
	}
	/**
	 * Return path info
	 *
	 * @return string
	 */
	private function getPathInfo()
	{
		return $this->request()->getPathInfoWithInex();
	}
	/**
	 * Return host
	 *
	 * @return string
	 */
	private function getHost()
	{
		return $this->request()->getHost();
	}
	/**
	 * Return site collection
	 *
	 * @return Collection
	 */
	public function sites()
	{
		return $this->site;
	}
	/**
	 * Add site
	 *
	 * @return \BX\MVC\SiteController
	 * @throws InvalidArgumentException
	 */
	public function addSite()
	{
		foreach(func_get_args() as $site){
			if (!($site instanceof SiteEntity)){
				throw new InvalidArgumentException('Error site must be Site type');
			}
			if (!$site->checkFields()){
				$this->log()->error('Site add error: '.implode(',',$site->getErrors()->all()));
				throw new InvalidArgumentException('Error validate site');
			}
			$this->site->add($site);
		}
		return $this;
	}
	/**
	 * Return current site
	 *
	 * @return null|Site
	 */
	protected function findSite($uri)
	{
		$uri = $this->getHost().$uri;
		foreach($this->sites() as $site){
			foreach((array)$site->regex as $rule){
				if (preg_match("/^$rule/",$uri)){
					$this->setSiteName($site->name);
					return $site;
				}
			}
		}
		return null;
	}
	/**
	 * Set current layout
	 *
	 * @param array $layout_rules
	 * @param string $page
	 */
	protected function findLayout(array $layout_rules,$page)
	{
		foreach($layout_rules as $key => $rules){
			foreach($rules as $rule){
				if (preg_match("/^$rule/",$page)){
					$this->setLayout($key);
				}
			}
		}
	}
	/**
	 * Render page
	 *
	 * @param SiteEntity $site
	 * @param type $page
	 * @param array $params
	 * @return type
	 */
	private function renderPage(SiteEntity $site,$page,array $params)
	{
		$folder = '/'.trim($site->folder,'/');
		$len_site = $this->string()->length($folder);
		if (!strncmp($page,$folder,$len_site)){
			$page = substr($page,$len_site);
		}
		foreach((array)$site->regex as $regex => $path){
			if (preg_match("/^$regex/",$page)){
				$page = $path;
			}
		}
		$path = $this->getSiteFolder().DIRECTORY_SEPARATOR.$this->getSiteName().
			DIRECTORY_SEPARATOR.$page;
		$params['page'] = $this->view()->render($path,$params);
		if ($this->getLayout() === false){
			$this->findLayout($site->layout_rule,$page);
		}
		if ($this->getLayout()){
			$path = $this->layout_folder.DIRECTORY_SEPARATOR.$this->getLayout().
				DIRECTORY_SEPARATOR.$this->main_template_file;
			return $this->view()->render($path,$params);
		}else{
			return $params['page'];
		}
	}
	/**
	 * Render response
	 *
	 * @param string $page
	 * @param array $params
	 * @return Response
	 * @throws Exception
	 */
	public function render($page = false,array $params = [])
	{
		$this->session()->load();
		if ($page === false){
			$page = $this->getPathInfo();
		}
		if ($this->config()->isDevMode()){
			set_error_handler(function($errno,$errstr,$errfile,$errline){
				if (0 === error_reporting()){
					return false;
				}
				throw new \ErrorException($errstr,0,$errno,$errfile,$errline);
			});
		}
		try{
			$this->log('mvc.manager.site_controller')->debug("start render `".$this->getHost().$page."`");
			$this->fire('BeforeRender');
			if ($this->getSiteName() === false){
				$site = $this->findSite($page);
				if ($site === null){
					throw new \RuntimeException('Site not found',500);
				}
			}
			foreach((array)$site->url_rewrite as $path => $regex){
				if (preg_match("/^$regex/",$page)){
					$page = $path;
				}
			}
			$content = $this->renderPage($site,$page,$params);
			$this->fire('afterRender',[&$content]);
			$this->view()->send($content);
			$this->log('mvc.manager.site_controller')->debug('end render');
			$return = $this->view()->response();
			$this->session()->save();
			return $return;
		}catch (\Exception $e){
			$this->log()->error($e->getMessage());
			$this->session()->save();
			return $this->getRenderException()->render($e,$this);
		}
	}
	/**
	 * Run render site
	 *
	 * @return \BX\MVC\SiteController
	 */
	public static function run()
	{
		$instanse = new static();
		return $instanse->render();
	}
}