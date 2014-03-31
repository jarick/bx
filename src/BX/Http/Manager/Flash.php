<?php
namespace BX\Http\Manager;
use BX\Manager;
use BX\Http\Manager\Session;

class Flash extends Manager
{
	const VALUE = 'value';
	const IS_MULTY = 'is_multy';
	const FLASH_KEY = 'BX.Http.Manager.Flash';
	protected static $data = [];
	protected static $save_data = [];
	protected static $start = false;
	protected $session = null;
	/**
	 * Set session manager
	 * @param Session $session
	 */
	public function setSession($session)
	{
		$this->session = $session;
	}
	/**
	 * Get session manager
	 * @return Session
	 */
	public function getSession()
	{
		if ($this->session === null){
			$this->session = Session::getManager();
		}
		return $this->session;
	}
	/**
	 * Init session
	 */
	public function init()
	{
		if (!self::$start){
			$save = $this->getSession()->get(self::FLASH_KEY);
			if (!empty($save)){
				foreach ($save as $key => $aFlash){
					self::$data[$key] = $aFlash[self::VALUE];
					if($aFlash[self::IS_MULTY] === true){
						self::$save_data[$key] = $aFlash;
					}
				}
			}
			$this->getSession()->get(self::FLASH_KEY,self::FLASH_KEY,self::$save_data);
			self::$start = true;
		}
	}

	public function set($key,$value,$multy = false)
	{
		self::$data[$key] = $value;
		self::$save_data[$key] = array(
			self::VALUE => $value,
			self::IS_MULTY => $multy
		);
		$this->getSession()->set(self::FLASH_KEY,self::$save_data);
	}

	public function get($key)
	{
		if (array_key_exists($key,self::$data)){
			return self::$data[$key];
		}
		return null;
	}
}