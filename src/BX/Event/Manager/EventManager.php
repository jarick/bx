<?php
namespace BX\Event\Manager;
use BX\Manager;
use BX\Event\IEvent;
use Illuminate\Events\Dispatcher;
use BX\Registry;

class EventManager extends Manager implements IEvent
{
	private function getDispatcher()
	{
		static $oManager;
		if(!isset($oManager)){
			if(Registry::exists('event')){
				$oDispatcher = Registry::get('event'); 
				if(is_string($oDispatcher)){
					$oManager = new $oDispatcher();
				} else{
					$oManager = $oDispatcher;
				}
			} else{
				$oManager = new Dispatcher();
			}
		}
		return $oManager;
	}
	
	public function on($sName,$oFunc,$iSort = 500)
	{
		return $this->getDispatcher()->listen($sName,$oFunc,$iSort);
	}
	
	public function fire($sName,$aParams,$bHalt = true)
	{
		return $this->getDispatcher()->fire($sName,$aParams,$bHalt);
	}
}