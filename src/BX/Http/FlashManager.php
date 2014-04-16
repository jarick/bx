<?php namespace BX\Http;

class FlashManager extends Manager
{
	const FLASH_KEY = 'BX.Http.Flash';
	protected static $data = [];
	protected static $save_data = [];
	protected static $start = false;
	private $store;
	/**
	 * Set store
	 * @param IStore $store
	 */
	public function setStore($store)
	{
		$this->store = $store;
	}
	/**
	 * Get store
	 * @return IStore
	 */
	private function store()
	{
		if ($this->store === null){
			#$this->store = new Session::getManager();
		}
		return $this->store;
	}
	/**
	 * Set flash message to store
	 * @param mixed $value
	 * @return \BX\Http\FlashManager
	 */
	public function set($value)
	{
		$this->store()->set(self::FLASH_KEY,$value);
		return $this;
	}
	/**
	 * Get flash message from store
	 * @return mixed
	 */
	public function get()
	{
		return $this->store()->get(self::FLASH_KEY);
	}
}