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
	public function on($name,$func,$sort = 500)
	{
		$this->getEventManager()->on($name,$func,$sort);
		return $this;
	}
	public function fire($name,$params = [],$halt = true)
	{
		return $this->getEventManager()->fire($name,$params,$halt);
	}
}