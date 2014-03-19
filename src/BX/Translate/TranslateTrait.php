<?php namespace BX\Translate;
use BX\Translate\Manager\Translate;
use BX\DI;
use BX\Registry;

trait TranslateTrait
{
	/**
	 * Get translate manager
	 * @return Translate
	 */
	private function getTranslateManager()
	{
		$manager = 'translate';
		if (DI::get($manager) === null){
			DI::set($manager,Translate::getManager());
		}
		return DI::get($manager);
	}
	/**
	 * Get translator
	 * @return Translate
	 */
	public function translator()
	{
		return $this->getTranslateManager();
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
		if ($package === null){
			$package = static::getPackage();
		}
		if ($service === null){
			$service = static::getService();
		}
		if ($lang === null){
			$lang = (Registry::exists('lang')) ? Registry::get('lang') : 'en';
		}
		return $this->getTranslateManager()->trans($message,$params,$lang,$package,$service);
	}
}