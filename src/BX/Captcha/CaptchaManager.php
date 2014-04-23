<?php namespace BX\Captcha;
use BX\Base\Registry;
use BX\Captcha\Entity\CaptchaEntity;
use BX\Captcha\Store\ICaptchaStore;
use BX\Captcha\Store\TableCaptchaStore;

class CaptchaManager
{
	/**
	 * @var CaptchaEntity
	 */
	private $entity;
	/**
	 * Get store
	 * @return ICaptchaStore
	 */
	private function store()
	{
		if (Registry::exists('captcha','store')){
			$store = Registry::get('captcha','store');
			switch ($store){
				case 'db': return new TableCaptchaStore($this->entity->unique_id);
				default : throw new \RuntimeException('Store `$store` is not found');
			}
		}else{
			return new TableCaptchaStore($this->entity->unique_id);
		}
	}
	/**
	 * Constructor
	 * @param type $unique_id
	 */
	public function __construct($unique_id)
	{
		$this->entity = $this->store()->getByUniqueId($unique_id);
	}
	/**
	 * Reaload current captcha
	 */
	public function reload($unique_id = null)
	{
		if ($unique_id === null){
			$unique_id = $this->entity->unique_id;
		}
		$this->store()->delete($this->entity);
		$this->entity = $this->store()->create($unique_id);
	}
	/**
	 * Check code
	 * @param string $sid
	 * @param string $code
	 * @return boolean
	 */
	public function check($sid,$code)
	{
		$unique_id = $this->entity->unique_id;
		$return = $this->entity->check($sid,$code);
		$this->reload($unique_id);
		return $return;
	}
	/**
	 * Get captcha entity
	 * @return CaptchaEntity
	 */
	public function getEntity()
	{
		return $this->entity;
	}
	/**
	 * Clear old captches
	 * @param type $day
	 * @return type
	 */
	public function clear($day = null)
	{
		if ($day === null){
			$day = Registry::get('captcha','day');
		}
		return $this->store()->clear($day);
	}
}