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
	 * @var ICaptchaStore
	 */
	private $store = null;
	/**
	 * Get store
	 * @return ICaptchaStore
	 */
	private function store($unique_id = null)
	{
		if ($this->store === null){
			if ($unique_id === null){
				$unique_id = $this->entity->unique_id;
			}
			if (Registry::exists('captcha','store')){
				$store = Registry::get('captcha','store');
				switch ($store){
					case 'db': $this->store = new TableCaptchaStore($unique_id);
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableCaptchaStore($unique_id);
			}
		}
		return $this->store;
	}
	/**
	 * Constructor
	 * @param string $unique_id
	 */
	public function __construct($unique_id)
	{
		$this->entity = $this->store($unique_id)->getByUniqueId($unique_id);
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
		if (!$return){
			$error = $this->entity->getErrors()->get('CODE');
		}
		$this->reload($unique_id);
		if (!$return){
			$this->entity->addError('CODE',$error[0]);
		}
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