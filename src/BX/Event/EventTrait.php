<?php namespace BX\Event;
use BX\Event\EventManager;
use BX\Base\DI;

trait EventTrait
{
	/**
	 * Get event manager
	 * @return EventManager
	 */
	private function event()
	{
		$key = 'event';
		if (DI::get($key) === null){
			DI::set($key,new EventManager());
		}
		return DI::get($key);
	}
	/**
	 * Listner
	 * @param string $name
	 * @param string $func
	 * @param integer $sort
	 * @return mixed
	 */
	public function on($name,$func,$sort = 500)
	{
		$this->event()->on($name,$func,$sort);
		return $this;
	}
	/**
	 * Call event
	 * @param string $name
	 * @param array $params
	 * @param boolean $halt
	 * @return mixed
	 */
	public function fire($name,$params = [],$halt = true)
	{
		return $this->event()->fire($name,$params,$halt);
	}
}