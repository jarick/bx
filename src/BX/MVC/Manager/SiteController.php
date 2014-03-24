<?php namespace BX\MVC\Manager;
use BX\Manager;
use BX\MVC\Entity\Site;
use BX\MVC\Exception\Exception;
use BX\Collection;
use BX\DI;
use BX\Registry;

class SiteController extends Manager
{
	use \BX\String\StringTrait;
	/**
	 * @var View
	 */
	private $view = false;
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
	 * @return View
	 */
	public function view()
	{
		return $this->view;
	}
	/**
	 * @return View
	 */
	public function getView()
	{
		return $this->view;
	}
	public function getSiteFolder()
	{
		return $this->site_folder;
	}
	public function getSiteName()
	{
		return $this->site_name;
	}
	public function setSiteName($site)
	{
		$this->fire('set site name',[&$site]);
		$this->log()->debug("set site name `$site`");
		$this->getView()->loadMeta('sites',$site);
		return $this->site_name = $site;
	}
	public function getLayout()
	{
		return $this->layout;
	}
	public function setLayout($layout)
	{
		$this->fire('SetLayout',[&$layout]);
		$this->layout = $layout;
	}
	public function init()
	{
		$manager = 'view';
		if (DI::get($manager) === null){
			DI::set($manager,View::getManager());
		}
		$this->view = DI::get($manager);
		$this->site = new Collection(get_class());
		if (Registry::exists('sites')){
			foreach (Registry::get('sites') as $site){
				$entity = Site::getEntity(false,['data' => $site]);
				$this->addSite($entity);
			}
		}
	}
	protected function getUri()
	{
		$request = $this->getView()->request();
		$uri = $request->getHost().$request->getPathInfo();
		if (substr($uri,-1) === '/'){
			$uri .= 'index';
		}
		return trim($uri,'/');
	}
	protected function isPregMatchUri($rule,$nead)
	{
		return preg_match("/^$rule/",$nead);
	}
	private function getPathInfo()
	{
		return $this->getView()->request()->getPathInfoWithInex();
	}
	private function getHost()
	{
		return $this->getView()->request()->getHost();
	}
	public function sites()
	{
		return $this->site;
	}
	public function addSite()
	{
		foreach (func_get_args() as $site){
			if (!($site instanceof Site)){
				throw new \InvalidArgumentException('Error site must be Site type');
			}
			if (!$site->checkFields()){
				var_dump($site->getErrors());
				die();
				throw new \InvalidArgumentException('Error validate site');
			}
			$this->site->attach($site);
		}
		return $this;
	}
	protected function findSite()
	{
		$uri = $this->getHost().$this->getPathInfo();
		foreach ($this->sites() as $site){
			foreach ((array) $site->getValue(Site::C_REGEX) as $sRule){
				if ($this->isPregMatchUri($sRule,$uri)){
					$this->setSiteName($site->getValue(Site::C_NAME));
					return $site;
				}
			}
		}
		return false;
	}
	protected function findLayout(array $layout_rules,$page)
	{
		foreach ($layout_rules as $key => $rules){
			foreach ($rules as $rule){
				if ($this->isPregMatchUri($rule,$page)){
					$this->setLayout($key);
					return $key;
				}
			}
		}
		return false;
	}
	private function renderPage(Site $site,$page,array $params)
	{
		if ($page === false){
			$page = $this->getPathInfo();
			$folder = '/'.trim($site->getValue(Site::C_FOLDER),'/');
			$len_site = $this->string()->length($folder);
			if (!strncmp($page,$folder,$len_site)){
				$page = substr($page,$len_site);
			}
		}
		foreach ((array) $site->getValue(Site::C_REGEX) as $regex => $path){
			if ($this->isPregMatchUri($regex,$page)){
				$page = $path;
			}
		}
		$path = $this->getSiteFolder().DS.$this->getSiteName().DS.$page;
		$params['page'] = $this->getView()->render($path,$params);
		if ($this->getLayout() === false){
			$this->findLayout((array) $site->getValue(Site::C_LAYOUT_RULE),$page);
		}
		if ($this->getLayout()){
			$path = $this->layout_folder.DS.$this->getLayout().DS.$this->main_template_file;
			return $this->getView()->render($path,$params);
		} else{
			return $params['page'];
		}
	}
	/**
	 * Render response
	 * @param string $page
	 * @param array $params
	 * @return \BX\Http\Manager\Response
	 * @throws Exception
	 */
	public function render($page = false,array $params = [])
	{
		try{
			$this->log()->debug("start render `".$this->getHost().$this->getPathInfo()."`");
			$this->fire('BeforeRender');
			if ($this->getSiteName() === false){
				$site = $this->findSite();
				if ($site === false){
					throw new Exception('Site not found',500);
				}
			}
			$sContent = $this->renderPage($site,$page,$params);
			$this->fire('afterRender',[&$sContent]);
			$this->getView()->send($sContent);
			$this->log()->debug('end render');
			return $this->getView()->response();
		} catch (Exception $e){
			$this->getView()->buffer()->flush();
			if (strlen($e->getMessage()) > 0){
				$this->log()->error($e->getMessage());
			}
			return $e->render($this);
		} catch (\Exception $e){
			$this->getView()->buffer()->flush();
			$this->log()->error($e->getMessage());
			$eExection = new Exception($e->getMessage(),500);
			return $eExection->render($this);
		}
	}
}