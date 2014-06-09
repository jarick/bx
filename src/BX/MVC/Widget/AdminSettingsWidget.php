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
	 * Run widget
	 */
	public function run()
	{
		$site = $this->getSite();
		$data = Config::get('sites',$site);
		$entity = new SiteEntity();
		$entity->setData($data);
		$post = $this->request()->post()->get('FORM');
		if ($post !== null){
			$post = array_map('trim',$post);
			$entity->setData($post);
			if ($entity->checkFields()){
				$save = [];
				foreach($entity->getData() as $key => $value){
					$save[$this->string()->toLower($key)] = $value;
				}
				$data = array_replace($data,$save);
				$yml = Config::all();
				$yml['sites'][$site] = $data;
				file_put_contents($this->getRealPath($this->path_to_config),Yaml::dump($yml));
				$this->redirect($this->getCurPageParam([],['post']));
			}
		}
		$flash = null;
		$this->render('admin/user/settings',compact('entity','flash'));
	}
}