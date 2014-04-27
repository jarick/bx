<?php namespace BX\Captcha\Store;

interface ICaptchaStore
{
	public function check($guid,$code);
	public function clear($id);
	public function clearOld($day);
	public function create();
	public function getByGuid($guid);
}