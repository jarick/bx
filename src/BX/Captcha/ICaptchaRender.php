<?php namespace BX\Captcha;

interface ICaptchaRender
{
	/**
	 * @return ICaptchaRender
	 */
	public function create($code);
	public function output();
}