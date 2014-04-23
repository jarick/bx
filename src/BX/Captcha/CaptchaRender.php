<?php namespace BX\Captcha;
use \Gregwar\Captcha\CaptchaBuilder;

class CaptchaRender implements ICaptchaRender
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
			$this->builder = $this->create($code);
		}else{
			$this->builder = \BX\Base\DI::get('captcha_render')->create($code);
		}
	}
	/**
	 * Create render
	 * @param string $code
	 * @return ICaptchaRender
	 */
	public function create($code)
	{
		return CaptchaBuilder::create($code)->build();
	}
	/**
	 * Render captcha
	 */
	public function output()
	{
		$this->builder->output();
	}
}