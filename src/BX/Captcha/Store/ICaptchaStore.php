<?php namespace BX\Captcha\Store;

interface ICaptchaStore
{
	public function getByUniqueId($unique_id);
	public function clear($day);
}