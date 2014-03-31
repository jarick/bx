<?php namespace BX\MVC;
use BX\Object;
use BX\MVC\Manager\View;

abstract class Widget extends Object
{
	use \BX\String\StringTrait;
	const ON_BEFORE_RENDER_WIDGET = 'BeforeRenderWidget';
	const ON_AFTER_RENDER_WIDGET = 'AfterRenderWidget';
	const WIDGETS_DIR = 'widgets/';
	protected $sWidgetFolder = 'widgets';
	/**
	 * @var View
	 */
	protected $oView;
	public function setView($oView)
	{
		$this->oView = $oView;
	}
	public function view()
	{
		return $this->oView;
	}
	public function getCurPageParam($aParam = [],$aParamKill = [])
	{
		$oRequest = $this->request();
		$query = $oRequest->query()->all();
		foreach($aParamKill as $sParam){
			unset($query[$sParam]);
		}
		$aParam = array_merge($query,$aParam);
		$sPath = $oRequest->getScript().$oRequest->getPathInfo();
		if (!empty($aParam)){
			$sPath .= '?';
		}
		foreach($aParam as $sKey => &$sValue){
			$sValue = $sKey.'='.$sValue;
		}
		return $sPath.implode('&',$aParam);
	}
	public function flash()
	{
		return $this->view()->flash();
	}
	public function session()
	{
		return $this->view()->session();
	}
	public function getSessionId()
	{
		return $this->session()->getSessionId();
	}
	public function request()
	{
		return $this->view()->request();
	}
	public function appendMeta($sKey,$sValue)
	{
		$this->view()->appendMeta($sKey,$sValue);
	}
	public function setMeta($sKey,$sValue)
	{
		$this->view()->setMeta($sKey,$sValue);
	}
	public function redirect($sUrl,$iStatus = 200)
	{
		$this->view()->redirect($sUrl,$iStatus);
	}
	public function render($file = false,$params = [])
	{
		$params['widget'] = $this;
		if ($file === false){
			$file = 'console/console';
		}
		echo $this->view()->render(self::WIDGETS_DIR.$file,$params);
	}
	static protected function getRegexClass()
	{
		return "/(\w+)\\\\(\w+)\\\\Widget\\\\(\w+)/";
	}
	static protected function getClass($sPackage,$sModule,$sWidget)
	{
		return $sPackage."\\".$sModule."\\Widget\\".$sWidget;
	}
	public function execRun($aParams)
	{
		foreach($aParams as $sKey => $sValue){
			$sFunc = 'set';
			foreach(explode('_',$sKey) as $sItem){
				$sFunc .= $this->string()->ucwords($sItem);
			}
			$this->$sFunc($sValue);
		}
		$this->fire(self::ON_BEFORE_RENDER_WIDGET,[&$this]);
		$this->log()->debug('start widget `'.get_class($this).'`');
		$this->run();
		$this->log()->debug('end widget `'.get_class($this).'`');
		$this->fire(self::ON_AFTER_RENDER_WIDGET,[&$this]);
	}
	public static function widget($oView,$aParams = [],$sWidget = false)
	{
		$aParams['view'] = $oView;
		$oWidget = static::autoload($sWidget,'widgets');
		$oWidget->execRun($aParams);
		#return $oWidget;
	}
	abstract public function run();
}