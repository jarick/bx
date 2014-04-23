<?php namespace BX\Captcha;
use \Gregwar\Captcha\CaptchaBuilder;

class CaptchaRender
{
	/**
	 * @var ICaptchaRender
	 */
	private $builder;
	/**
	 * Constructor
	 */
	public function __construct($code)
	{
		if (\BX\Base\DI::get('captcha_render') === null){
			$this->builder = CaptchaBuilder::create($code)->build();
		}else{
			$this->builder = \BX\Base\DI::get('captcha_render')->create($code);
		}
	}
	/**
	 * Render captcha
	 */
	public function output()
	{
		$this->builder->output();
	}
}