<?php namespace BX\Counter;
use \BX\Captcha\Store\ICaptchaStore;
use \BX\Counter\Store\ICounterStore;
use \BX\Counter\Store\TableCounterStore;
use \BX\Base\Registry;

class CounterManager
{
	/**
	 * @var string
	 */
	private $entity;
	/**
	 *
	 * @var ICounterStore
	 */
	private $store = null;
	/**
	 * Constructor
	 * @param string $entity
	 */
	public function __construct($entity)
	{
		$this->entity = $entity;
	}
	/**
	 * Get store
	 * @return ICaptchaStore
	 */
	private function store()
	{
		if ($this->store === null){
			if (Registry::exists('counter','store')){
				$store = Registry::get('counter','store');
				switch ($store){
					case 'db': $this->store = new TableCounterStore($this->entity);
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableCounterStore($this->entity);
			}
		}
		return $this->store;
	}
	/**
	 * Increment counter
	 * @param string $entity_id
	 * @return integer
	 */
	public function inc($entity_id)
	{
		return $this->store()->inc($this->entity,$entity_id);
	}
	/**
	 * Clear counter
	 * @param string $entity_id
	 * @return true
	 */
	public function clear($entity_id)
	{
		return $this->store()->clear($this->entity,$entity_id);
	}
	/**
	 * Clear old counter
	 * @param integer $day
	 * @return true
	 */
	public function clearOld($day = 30)
	{
		return $this->store()->clearOld($day);
	}
	/**
	 * Get entity
	 * @param string $entity_id
	 * @return \BX\Captcha\Entity\CaptchaEntity
	 */
	public function get($entity_id)
	{
		return $this->store()->get($this->entity,$entity_id);
	}
}