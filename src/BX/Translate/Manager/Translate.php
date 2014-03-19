<?php
namespace BX\Translate\Manager;
use BX\Manager;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;


class Translate extends Manager
{
	/**
	 * @var string
	 */
	protected $lang = 'en';
	/**
	 * @var array
	 */
	protected $files = [];
	/**
	 *
	 * @var Translator
	 */
	private static $adaptor;

	public function setLocale($lang)
	{
		$this->lang = $lang;
	}
	/**
	 * Init
	 */
	public function init()
	{
		if(!isset(self::$adaptor)){
			self::$adaptor = new Translator($this->lang);
			self::$adaptor->addLoader('array', new ArrayLoader());
		}
	}
	/**
	 * Get translator
	 * @return Translator
	 */
	private function adaptor()
	{
		return self::$adaptor;
	}
	/**
	 * Load messages from php class
	 * @param string $lang
	 * @param string $package
	 * @param string $service
	 */
	private function load($lang,$package,$service)
	{
		$key = $package.'.'.$service.'.'.$lang;
		if(!array_key_exists($key, $this->files))
		{
			$class = $package."\\".$service."\\Message\\".ucwords($lang);
			if(class_exists($class)){
				$aMessage = call_user_func([$class,'get']);
				if(!empty($aMessage)){
					$this->adaptor()->addResource('array',$aMessage, $lang);
				}
			}
			$this->files[] = $key;
		}
	}

	public function addArrayResource($resource, $locale = 'en')
	{
		$this->adaptor()->addResource('array', $resource, $locale);
	}

	public function trans($message,$params,$lang,$package,$service)
	{
		$this->load($lang, $package, $service);
		return $this->adaptor()->trans($message,$params);
	}

	public function choice($message,$number,$params,$lang,$package,$service)
	{
		$this->load($lang, $package, $service);
		return $this->adaptor()->transChoice($message,$number,$params);
	}
}