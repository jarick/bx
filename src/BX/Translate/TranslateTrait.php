<?php namespace BX\Translate;
use \BX\Translate\TranslateManager;
use BX\Config\DICService;
use BX\Config\Config;

trait TranslateTrait
{
	/**
	 * Get translator
	 *
	 * @return TranslateManager
	 */
	protected function translator()
	{
		$name = 'translate';
		if (DICService::get($name) === null){
			$manager = function(){
				return new TranslateManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
	/**
	 * Translate message
	 *
	 * @params string $message
	 * @params array $params
	 * @params string $lang
	 * @params string $package
	 * @params string $service
	 * @return string
	 */
	protected function trans($message,array $params = [],$lang = null,$package = null,$service = null)
	{
		$class_array = explode('\\',get_called_class());
		if ($package === null){
			$package = $class_array[0];
		}
		if ($service === null){
			if (!isset($class_array[1])){
				return $message;
			}else{
				$service = $class_array[1];
			}
		}
		if ($lang === null){
			if (Config::exists('lang')){
				$lang = Config::get('lang');
			}else{
				$lang = 'en';
			}
		}
		return $this->translator()->trans($message,$params,$lang,$package,$service);
	}
}