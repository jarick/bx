<?php namespace BX\Event;
use BX\Event\Manager\EventManager;
use BX\DI;

trait EventTrait
{
	private function getEventManager()
	{
		$key = 'event';
		if (DI::get($key) === null){
			DI::set($key,EventManager::getManager());
		}
		return DI::get($key);
	}
	public function on($sName,$oFunc,$iSort = 500)
	{
		$this->getEventManager()->on($sName,$oFunc,$iSort);
		return $this;
	}
	public function fire($sName,$aParams = [],$bHalt = true)
	{
		return $this->getEventManager()->fire($sName,$aParams,$bHalt);
	}
}