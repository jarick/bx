<?php namespace BX\MVC;
use BX\Base\Collection;
use BX\Base\DI;
use BX\Base\Registry;
use BX\Http\Response;
use BX\MVC\Entity\SiteEntity;
use BX\MVC\Exception\RenderException;
use InvalidArgumentException;

class SiteController
{
	use \BX\String\StringTrait,
	 \BX\Event\EventTrait,
	 \BX\Http\HttpTrait,
	 \BX\Logger\LoggerTrait;
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
	 * Get view
	 * @return View
	 */
	public function view()
	{
		$manager = 'view';
		if (DI::get($manager) === null){
			DI::set($manager,new View());
		}
		return DI::get($manager);
	}
	/**
	 * Get render exception
	 * @return RenderException
	 */
	private function getRenderException()
	{
		$manager = 'render_exception';
		if (DI::get($manager) === null){
			DI::set($manager,new RenderException());
		}
		return DI::get($manager);
	}
	/**
	 * Get instance
	 * @return \BX\MVC\SiteController
	 */
	public static function getInstance()
	{
		return new static();
	}
	/**
	 * Get Site folder
	 * @return string
	 */
	public function getSiteFolder()
	{
		return $this->site_folder;
	}
	/**
	 * Get site name
	 * @return string
	 */
	public function getSiteName()
	{
		return $this->site_name;
	}
	/**
	 * Set site name
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
	 * Get layout name
	 * @return string
	 */
	public function getLayout()
	{
		return $this->layout;
	}
	/**
	 * Set layout
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
		if (Registry::exists('sites')){
			foreach(Registry::get('sites') as $site){
				$entity = new SiteEntity();
				$entity->setData($site);
				$this->addSite($entity);
			}
		}
	}
	/**
	 * Get path info
	 * @return string
	 */
	private function getPathInfo()
	{
		return $this->request()->getPathInfoWithInex();
	}
	/**
	 * Get host
	 * @return string
	 */
	private function getHost()
	{
		return $this->request()->getHost();
	}
	/**
	 * Get site collection
	 * @return Collection
	 */
	public function sites()
	{
		return $this->site;
	}
	/**
	 * Add site
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
	 * Get current site
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
		$path = $this->getSiteFolder().DS.$this->getSiteName().DS.$page;
		$params['page'] = $this->view()->render($path,$params);
		if ($this->getLayout() === false){
			$this->findLayout($site->layout_rule,$page);
		}
		if ($this->getLayout()){
			$path = $this->layout_folder.DS.$this->getLayout().DS.$this->main_template_file;
			return $this->view()->render($path,$params);
		}else{
			return $params['page'];
		}
	}
	/**
	 * Render response
	 * @param string $page
	 * @param array $params
	 * @return Response
	 * @throws Exception
	 */
	public function render($page = false,array $params = [])
	{
		if ($page === false){
			$page = $this->getPathInfo();
		}
		if (Registry::isDevMode()){
			set_error_handler(function($errno,$errstr,$errfile,$errline,array $errcontext){
				if (0 === error_reporting()){
					return false;
				}
				throw new \ErrorException($errstr,0,$errno,$errfile,$errline);
			});
		}
		try{
			$this->log()->debug("start render `".$this->getHost().$page."`");
			$this->fire('BeforeRender');
			if ($this->getSiteName() === false){
				$site = $this->findSite($page);
				if ($site === null){
					throw new \RuntimeException('Site not found',500);
				}
			}
			$sContent = $this->renderPage($site,$page,$params);
			$this->fire('afterRender',[&$sContent]);
			$this->view()->send($sContent);
			$this->log()->debug('end render');
			return $this->view()->response();
		}catch (\Exception $e){
			$this->log()->error($e->getMessage());
			return $this->getRenderException()->render($e,$this);
		}
	}
}