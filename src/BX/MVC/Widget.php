<?php namespace BX\MVC;
use BX\MVC\View;

abstract class Widget
{
	use \BX\String\StringTrait,
	 \BX\Http\HttpTrait,
	 \BX\Logger\LoggerTrait,
	 \BX\Event\EventTrait;
	const ON_BEFORE_RENDER_WIDGET = 'BeforeRenderWidget';
	const ON_AFTER_RENDER_WIDGET = 'AfterRenderWidget';
	const WIDGETS_DIR = 'widgets/';
	protected $sWidgetFolder = 'widgets';
	/**
	 * @var View
	 */
	protected $view;
	/**
	 * Set view
	 * @return Widget
	 */
	public function setView(IView $view)
	{
		$this->view = $view;
		return $this;
	}
	/**
	 * Get view
	 * @return View
	 */
	public function view()
	{
		return $this->view;
	}
	/**
	 * Return current page path with query params
	 * @param array $add_params
	 * @param array $kill_params
	 * @return string
	 */
	public function getCurPageParam(array $add_params = [],array $kill_params = [])
	{
		$request = $this->request();
		$query = $request->query()->all();
		foreach($kill_params as $param){
			unset($query[$param]);
		}
		$add_params = array_replace($query,$add_params);
		$path = $request->getPathInfo();
		if (!empty($add_params)){
			$path .= '?';
		}
		foreach($add_params as $key => &$value){
			if (is_string($value)){
				$value = $key.'='.urlencode($value);
			}else{
				$value = $key.'='.http_build_query($value);
			}
		}
		return $path.implode('&',$add_params);
	}
	/**
	 * Get session id
	 * @return string
	 */
	public function getSessionId()
	{

	}
	/**
	 * Redirect
	 * @param string $url
	 * @param integer $status
	 */
	public function redirect($url,$status = 302)
	{
		$this->view()->redirect($url,$status);
	}
	/**
	 * Render widget
	 * @param string $file
	 * @param array $params
	 */
	public function render($file,array $params = [])
	{
		$params['widget'] = $this;
		echo $this->view()->render(self::WIDGETS_DIR.$file,$params);
	}
	/**
	 * Execute run
	 * @param array $params
	 * @return Widget
	 */
	public function execRun(array $params = [])
	{
		foreach($params as $key => $value){
			$func = 'set';
			foreach(explode('_',$key) as $item){
				$func .= $this->string()->ucwords($item);
			}
			$this->$func($value);
		}
		$this->fire(self::ON_BEFORE_RENDER_WIDGET,[&$this]);
		$this->log()->debug('start widget `'.get_class($this).'`');
		$this->run();
		$this->log()->debug('end widget `'.get_class($this).'`');
		$this->fire(self::ON_AFTER_RENDER_WIDGET,[&$this]);
		return $this;
	}
	/**
	 * Render widget
	 * @param View $view
	 * @param array $params
	 * @return Widget
	 */
	public static function widget(IView $view,array $params = [])
	{
		$widget = new static();
		return $widget->setView($view)->execRun($params);
	}
	abstract public function run();
}