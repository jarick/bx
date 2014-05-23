<?php namespace BX\Captcha;
use BX\Config\DICService;

trait CaptchaTrait
{
	/**
	 * Get manager
	 *
	 * @return CaptchaManager
	 */
	protected function captcha()
	{
		$name = 'captcha';
		if (DICService::get($name) === null){
			$manager = function(){
				return new CaptchaManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}