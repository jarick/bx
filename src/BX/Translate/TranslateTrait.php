<?php
namespace BX\Translate;
use BX\Translate\Manager\Translate;
use BX\Registry;

trait TranslateTrait
{
	private function getTranslateManager()
	{
		static $oTranslate;
		if(!isset($oTranslate)){
			$sLang = (Registry::exists('lang')) ? Registry::get('lang') : 'en';
			$oTranslate = Translate::getManager(false,['locale' => $sLang]);
		}
		return $oTranslate;
	}
	
	public function translator()
	{
		return $this->getTranslateManager();
	}
	
	public function trans($sMessage,$aParams = [],$sLang = false,$sPackage = false,$sService=false)
	{
		if($sPackage === false){
			$sPackage = static::getPackage();
		}
		if($sService === false){
			$sService = static::getService();
		}
		if($sLang === false){
			$sLang = (Registry::exists('lang')) ? Registry::get('lang') : 'en';
		}
		return $this->getTranslateManager(false)->trans($sMessage, $aParams, $sLang,$sPackage,$sService);
	}	
	
	#public function choice($sMessage,$iNumber,$aParams = [],$sLang = false,$sPackage = false,$sService=false)
	#{
	#	
	#}
}