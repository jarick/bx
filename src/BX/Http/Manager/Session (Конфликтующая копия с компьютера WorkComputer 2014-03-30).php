<?php
namespace BX\Http\Manager;
use BX\Manager;

class Session extends Manager
{
	/**
	 * @var boolean
	 */
	private $start = false;
	/**
	 * @var array
	 */
	private $data = null;
	/**
	 * Is started
	 * @return boolean
	 */
	public function isStarted()
	{
		return $this->start;
	}
	/**
	 * Start session
	 * @return Session
	 */
	public function start()
    {
    	$this->start = true;
		session_start();
		$this->data = &$_SESSION;
		return $this;
	}
	/**
	 * Has session key
	 * @param type $sKey
	 * @return type
	 */
	public function has($sKey)
    {
    	if(!$this->isStarted()){
    		$this->start();
    	}
        return array_key_exists($sKey,$this->data);
	}
	/**
	 * Get session
	 * @param null|string $key
	 * @return array|string
	 */
	public function get($key = null)
	{
    	if(!$this->isStarted()){
    		$this->start();
    	}
    	if ($key === null){
			return $this->data;
		} else{
			return isset($this->data[$sKey]) ? $this->data[$sKey] : null;
		}
	}
	/**
	 * Set session
	 * @param string $key
	 * @param string $sValue
	 */
	public function set($key,$value)
	{
    	if(!$this->isStarted()){
    		$this->start();
    	}
    	$this->data[$key] = $value;
	}
	/**
	 * Get session id
	 * @return string
	 */
	public function getSessionId()
    {
    	if(!$this->isStarted()){
    		$this->start();
    	}
    	return session_id();
    }
}