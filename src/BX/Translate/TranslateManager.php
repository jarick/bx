<?php namespace BX\Translate;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use BX\Base\Registry;

class TranslateManager implements ITranslate
{
	/**
	 * @var string
	 */
	protected $lang = null;
	/**
	 * @var array
	 */
	protected $files = [];
	/**
	 *
	 * @var Translator
	 */
	private $adaptor = null;
	public function setLocale($lang)
	{
		$this->lang = $lang;
	}
	/**
	 * Init
	 */
	public function __construct()
	{
		if ($this->lang === null){
			$this->lang = (Registry::exists('lang')) ? Registry::get('lang') : 'en';
		}
		if ($this->adaptor === null){
			$this->adaptor = new Translator($this->lang);
			$this->adaptor->addLoader('array',new ArrayLoader());
		}
	}
	/**
	 * Get translator
	 * @return Translator
	 */
	private function adaptor()
	{
		return $this->adaptor;
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
		if (!array_key_exists($key,$this->files)){
			$class = $package."\\".$service."\\Message\\".ucwords($lang);
			if (class_exists($class)){
				$messages = call_user_func([$class,'get']);
				if (!empty($messages)){
					$this->adaptor()->addResource('array',$messages,$lang);
				}
			}
			$this->files[] = $key;
		}
	}
	/**
	 * Add array translate
	 * @param array $resource
	 * @param string $locale
	 * @return self
	 */
	public function addArrayResource(array $resource,$locale = null)
	{
		if ($locale === null){
			$locale = Registry::get('lang');
		}
		$this->adaptor()->addResource('array',$resource,$locale);
		return $this;
	}
	/**
	 * Translate message
	 * @param string $message
	 * @param array $params
	 * @param string $lang
	 * @param type $package
	 * @param type $service
	 * @return type
	 */
	public function trans($message,array $params = [],$lang = false,$package = false,$service = false)
	{
		$this->load($lang,$package,$service);
		return $this->adaptor()->trans($message,$params);
	}
	/**
	 * Translate choice message
	 * @param straing $message
	 * @param integer $number
	 * @param array $params
	 * @param string $lang
	 * @param string $package
	 * @param string $service
	 * @return string
	 */
	public function choice($message,$number,array $params = [],$lang = false,$package = false,$service = false)
	{
		$this->load($lang,$package,$service);
		return $this->adaptor()->transChoice($message,$number,$params);
	}
}