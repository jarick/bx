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
	 * Create
	 */
	public function create()
	{
		return $this->store()->create();
	}
	/**
	 * Check code
	 * @param string $guid
	 * @param string $code
	 * @return false|CaptchaEntity
	 */
	public function get($guid,$code)
	{
		return $this->store()->get($guid,$code);
	}
	/**
	 * Reaload current captcha
	 * @param integer $id
	 * @return CaptchaEntity
	 */
	public function reload($id)
	{
		return $this->store()->reload($id);
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
	 * @param type $day
	 * @return true
	 */
	public function clearOld($day = null)
	{
		if ($day === null){
			$day = Registry::get('captcha','day');
		}
		return $this->store()->clearOld($day);
	}
}