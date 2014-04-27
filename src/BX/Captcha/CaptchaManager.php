<?php namespace BX\Captcha;
use BX\Base\Registry;
use BX\Captcha\Entity\CaptchaEntity;
use BX\Captcha\Store\ICaptchaStore;
use BX\Captcha\Store\TableCaptchaStore;

class CaptchaManager
{
	/**
	 * @var ICaptchaStore
	 */
	private $store = null;
	/**
	 * Get store
	 * @return ICaptchaStore
	 */
	private function store()
	{
		if ($this->store === null){
			if (Registry::exists('captcha','store')){
				$store = Registry::get('captcha','store');
				switch ($store){
					case 'db': $this->store = new TableCaptchaStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableCaptchaStore();
			}
		}
		return $this->store;
	}
	/**
	 * Get guid
	 * @return string
	 */
	public function getGuid()
	{
		$captcha = $this->store()->create();
		return $captcha->guid;
	}
	/**
	 * Get code by guid
	 * @param string $guid
	 * @return string|null
	 */
	public function getCode($guid)
	{
		$captches = $this->store()->getByGuid($guid);
		if ($captches->count() > 0){
			return $captches->current()->code;
		}else{
			return null;
		}
	}
	/**
	 * Check code
	 * @param string $guid
	 * @param string $code
	 * @return false|CaptchaEntity
	 */
	public function check($guid,$code)
	{
		return $this->store()->check($guid,$code);
	}
	/**
	 * Reaload current captcha
	 * @param integer $id
	 * @return CaptchaEntity
	 */
	public function reload($id)
	{
		$captches = $this->store()->reload($id);
		return $captches->current()->code;
	}
	/**
	 * Clear captcha
	 * @param integer $id
	 * @return true
	 */
	public function clear($id)
	{
		return $this->store()->clear($id);
	}
	/**
	 * Clear old captches
	 * @param null|integer $day
	 * @return true
	 */
	public function clearOld($day = null)
	{
		if ($day === null){
			if (Registry::exists('captcha','day')){
				$day = Registry::get('captcha','day');
			}else{
				$day = 30;
			}
		}
		return $this->store()->clearOld($day);
	}
}