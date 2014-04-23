<?php namespace BX\Captcha\Widget;
use BX\Captcha\CaptchaManager;
use BX\Captcha\CaptchaRender;
use BX\MVC\Widget;

class CaptchaWidget extends Widget
{
	use \BX\Translate\TranslateTrait;
	public function run()
	{
		$this->view->buffer()->flush();
		try{
			$ip = $this->request()->server()->get('REMOTE_ADDR');
			$cpt = new CaptchaManager($ip);
			if ($this->request()->query()->has('reload')){
				$cpt->reload();
			}
			$builder = new CaptchaRender($cpt->getEntity()->code);
			$this->response()->headers['Content-type'] = 'image/jpeg';
			$builder->output();
		}catch (\Exception $e){
			$this->log('captcha.widget.captcha')->err($e);
			echo $this->trans('captcha.widget.captcha.error_generate_captcha');
		}
		$this->view->abort();
	}
}