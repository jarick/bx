<?php namespace BX\MVC\Widget;
use BX\MVC\Widget;
use BX\Config\Config;
use BX\MVC\Entity\SiteEntity;
use Symfony\Component\Yaml\Yaml;

class AdminSettingsWidget extends Widget
{
	/**
	 * @var string
	 */
	private $site = null;
	/**
	 * @var string
	 */
	private $path_to_config = '~/../config/main.yml';
	/**
	 * Set actual site
	 *
	 * @param string $site
	 * @return \BX\MVC\Widget\AdminSettingsWidget
	 */
	public function setSite($site)
	{
		$this->site = $site;
		return $this;
	}
	/**
	 * Set path to main yml config site
	 *
	 * @param string $path
	 * @return \BX\MVC\Widget\AdminSettingsWidget
	 */
	public function setPathToConfig($path)
	{
		$this->path_to_config = $path;
		return $this;
	}
	/**
	 * Return base site name
	 *
	 * @return string
	 */
	private function getSite()
	{
		if ($this->site === null){
			$this->site = $this->view()['base'];
		}
		return $this->site;
	}
	/**
	 * Get real path for file
	 *
	 * @param string $path
	 * @return string
	 */
	public function getRealPath($path)
	{
		$path = realpath(str_replace('~',$this->request()->server()->get('DOCUMENT_ROOT'),$path));
		if ($path === false){
			throw new \RuntimeException('Path to config is not correct');
		}
		return $path;
	}
	/**
	 * Recursive trim array values
	 *
	 * @param array $value
	 * @return array
	 */
	protected function trim(array $value)
	{
		foreach($value as &$item){
			if (is_array($item)){
				$item = $this->trim($item);
			}else{
				$item = trim($item);
			}
		}
		return $value;
	}
	/**
	 * Check post data
	 *
	 * @param array $post
	 * @param \BX\MVC\Entity\SiteEntity $entity
	 * @return boolean
	 */
	protected function checkFields(array &$post,SiteEntity $entity)
	{
		foreach(['REGEX'] as $field){
			if (array_key_exists($field,$post)){
				foreach($post[$field] as $key => $value){
					if ($this->string()->length($value) === 0){
						unset($post[$field][$key]);
					}
				}
			}
		}
		$entity->setData($post);
		$session_token = null;
		if (array_key_exists('SESSION_TOKEN',$post)){
			$session_token = $post['SESSION_TOKEN'];
		}
		if (intval($session_token) !== $this->session()->getId()){
			$entity->addError('UNKNOW',$this->trans('mvc.widgets.admin_settings.error_session_token'));
			return false;
		}
		return true;
	}
	/**
	 * Run widget
	 */
	public function run()
	{
		$site = $this->getSite();
		$data = Config::get('sites',$site);
		$entity = new SiteEntity();
		$post = $this->request()->post()->get('FORM');
		if ($post !== null){
			$post = $this->trim($post);
			if ($this->checkFields($post,$entity)){
				if ($entity->checkFields()){
					$save = [];
					foreach($entity->getData() as $key => $value){
						$save[$this->string()->toLower($key)] = $value;
					}
					$data = array_replace($data,$save);
					$yml = Config::all();
					$yml['sites'][$site] = $data;
					file_put_contents($this->getRealPath($this->path_to_config),Yaml::dump($yml));
					$this->setFlash($this->trans('mvc.widgets.admin_settings.success'));
					$this->redirect($this->getCurPageParam([],['post']));
				}
			}
		}else{
			$entity->setData($data);
		}
		$flash = $this->getFlash();
		$this->render('admin/user/settings',compact('entity','flash','post'));
	}
}