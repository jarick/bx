<?php namespace BX\Http\Manager;
use BX\Manager;

class Session extends Manager
{
	private $start = false;
	private $session = null;
	/**
	 * Set session array
	 * @param array $session
	 * @return Session
	 */
	public function setSession(array $session)
	{
		$this->session = $session;
		return $this;
	}
	/**
	 * Init
	 */
	public function init()
	{
		if ($this->session === null){
			$this->session = &$_SESSION;
		}
	}
	/**
	 * Start session
	 */
	public function start()
	{
		$this->start = true;
		session_start();
	}
	/**
	 * Has key in sesion
	 * @param string $key
	 * @return boolean
	 */
	public function has($key)
	{
		if (!$this->start){
			$this->start();
		}
		return array_key_exists($key,$this->session);
	}
	/**
	 * Get value in sesion by key
	 * @param string $key
	 * @return array|string
	 */
	public function get($key = false)
	{
		if (!$this->start){
			$this->start();
		}
		return ($key === false) ? $this->session : $this->session[$key];
	}
	/**
	 * Set value in sesion
	 * @param string $key
	 * @param string $value
	 * @return Session
	 */
	public function set($key,$value)
	{
		if (!$this->start){
			$this->start();
		}
		$this->session[$key] = $value;
		return $this;
	}
	/**
	 * Get session id
	 * @return string
	 */
	public function getSessionId()
	{
		if (!$this->start){
			$this->start();
		}
		return session_id();
	}
}