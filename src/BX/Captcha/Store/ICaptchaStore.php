<?php namespace BX\Captcha\Store;

interface ICaptchaStore
{
	public function get($guid,$code);
	public function clear($id);
	public function clearOld($day);
	public function create();
}