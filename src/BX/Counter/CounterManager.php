<?php namespace BX\Counter;
use \BX\Captcha\Store\ICaptchaStore;
use \BX\Counter\Store\ICounterStore;
use \BX\Counter\Store\TableCounterStore;
use \BX\Base\Registry;

class CounterManager
{
	const DEFAULT_DAY_SAVE = 30;
	/**
	 *
	 * @var ICounterStore
	 */
	private $store = null;
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
					case 'db': $this->store = new TableCounterStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableCounterStore();
			}
		}
		return $this->store;
	}
	/**
	 * Increment counter
	 * @param string $entity_id
	 * @return integer
	 */
	public function inc($entity,$entity_id)
	{
		return $this->store()->inc($entity,$entity_id);
	}
	/**
	 * Get entity
	 * @param string $entity_id
	 * @return integer
	 */
	public function get($entity,$entity_id)
	{
		$counter = $this->store()->get($entity,$entity_id);
		return ($counter === false) ? 0 : intval($counter->counter);
	}
	/**
	 * Clear counter
	 * @param string $entity
	 * @param string $entity_id
	 * @return true
	 */
	public function clear($entity,$entity_id)
	{
		return $this->store()->clear($entity,$entity_id);
	}
	/**
	 * Clear old counter
	 * @param integer $day
	 * @return true
	 */
	public function clearOld($day = null)
	{
		if ($day === null){
			if (Registry::exists('counter','day')){
				$day = Registry::get('counter','day');
			}else{
				$day = self::DEFAULT_DAY_SAVE;
			}
		}
		return $this->store()->clearOld($day);
	}
}