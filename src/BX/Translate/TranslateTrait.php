<?php
namespace BX\Translate;
use BX\Translate\Manager\Translate;
use BX\Registry;
use BX\DI;

trait TranslateTrait
{
	/**
	 * Get translate manager
	 * @return Translate
	 */
	private function getTranslateManager()
	{
		$manager = 'translate';
		if(DI::get($manager) === null){
			$lang = (Registry::exists('lang')) ? Registry::get('lang') : 'en';
			DI::set($manager,Translate::getManager(false,['locale' => $lang]));
		}
		return DI::get($manager);
	}
	
	public function translator()
	{
		return $this->getTranslateManager();
	}
	
	public function trans($message,$params = [],$lang = false,$package = false,$service=false)
	{
		if($package === false){
			$package = static::getPackage();
		}
		if($service === false){
			$service = static::getService();
		}
		if($lang === false){
			$lang = (Registry::exists('lang')) ? Registry::get('lang') : 'en';
		}
		return $this->getTranslateManager(false)->trans($message, $params, $lang,$package,$service);
	}	
}