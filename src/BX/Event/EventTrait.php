<?php namespace BX\Event;
use BX\Config\DICService;

trait EventTrait
{
	/**
	 * Get event manager
	 * @return EventManager
	 */
	private function event()
	{
		$name = 'event';
		if (DICService::get($name) === null){
			$manager = function(){
				return new EventManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
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