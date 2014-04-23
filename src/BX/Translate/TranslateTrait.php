<?php namespace BX\Translate;
use \BX\Translate\TranslateManager;
use BX\Base\DI;
use BX\Base\Registry;

trait TranslateTrait
{
	/**
	 * Get translator
	 * @return TranslateManager
	 */
	public function translator()
	{
		$manager = 'translate';
		if (DI::get($manager) === null){
			DI::set($manager,new TranslateManager());
		}
		return DI::get($manager);
	}
	/**
	 * Translate message
	 * @params string $message
	 * @params array $params
	 * @params string $lang
	 * @params string $package
	 * @params string $service
	 * @return string
	 */
	public function trans($message,array $params = [],$lang = null,$package = null,$service = null)
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
			$lang = (Registry::exists('lang')) ? Registry::get('lang') : 'en';
		}
		return $this->translator()->trans($message,$params,$lang,$package,$service);
	}
}