<?php
namespace BX\Http\Manager;
use BX\Manager;
use BX\Http\Manager\Flash;

class Session extends Manager
{
	protected $bStart;
	
	public function isStarted()
	{
		return $this->bStart;
	}
	
 	public function start()
    {
    	$this->bStart = true;
        session_start();
        Flash::getManager()->recalc();
    }

    public function has($sKey)
    {
    	if(!$this->isStarted()){
    		$this->start();
    	}
        return array_key_exists($sKey, $_SESSION);
    }

    public function get($sKey = false)
    {
    	if(!$this->isStarted()){
    		$this->start();
    	}
    	return ($sKey === false) ? $_SESSION : $_SESSION[$sKey];
    }

    public function set($sKey, $sValue)
    {
    	if(!$this->isStarted()){
    		$this->start();
    	}
    	$_SESSION[$sKey] = $sValue;
    }
    
    public function getSessionId()
    {
    	if(!$this->isStarted()){
    		$this->start();
    	}
    	return session_id();
    }
}