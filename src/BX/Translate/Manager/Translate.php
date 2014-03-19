<?php
namespace BX\Translate\Manager;
use BX\Manager;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;


class Translate extends Manager
{
	protected $sLang = 'en';
	protected $aFiles = [];
	private static $adaptor;

	public function setLocale($sLang)
	{
		$this->sLang = $sLang;
	}

	public function init()
	{
		if(!isset(self::$adaptor)){
			self::$adaptor = new Translator($this->sLang);
			self::$adaptor->addLoader('array', new ArrayLoader());
		}
	}

	private function adaptor()
	{
		return self::$adaptor;
	}

	private function load($sLang,$sPackage,$sService)
	{
		$sKey = $sPackage.'.'.$sService.'.'.$sLang;
		if(!array_key_exists($sKey, $this->aFiles))
		{
			$sClass = $sPackage."\\".$sService."\\Message\\".ucwords($sLang);
			if(class_exists($sClass)){
				$aMessage = call_user_func([$sClass,'get']);
				if(!empty($aMessage)){
					$this->adaptor()->addResource('array',$aMessage, $sLang);
				}
			}
			$this->aFiles[] = $sKey;
		}
	}

	public function addArrayResource($resource, $locale = 'en')
	{
		$this->adaptor()->addResource('array', $resource, $locale);
	}

	public function trans($sMessage,$aParams,$sLang,$sPackage,$sService)
	{
		$this->load($sLang, $sPackage, $sService);
		return $this->adaptor()->trans($sMessage,$aParams);
	}

	public function choice($sMessage,$iNumber,$aParams,$sLang,$sPackage,$sService)
	{
		$this->load($sLang, $sPackage, $sService);
		return $this->adaptor()->transChoice($sMessage,$iNumber,$aParams);
	}
}