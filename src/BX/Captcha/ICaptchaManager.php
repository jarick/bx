<?php namespace BX\Captcha;

interface ICaptchaManager
{
	public function getGuid();
	public function getCode($guid);
	public function check($guid,$code);
	public function reload($id);
	public function clear($id);
	public function clearOld($day = null);
}