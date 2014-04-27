<?php namespace BX\Event;
use BX\Event\IEvent;
use Illuminate\Events\Dispatcher;
use BX\Base\Registry;
use BX\Base\DI;

class EventManager implements IEvent
{
	/**
	 * Get dispatcher
	 * @return Dispatcher
	 */
	private function getDispatcher()
	{
		if (DI::get('event_dispatcher') === null){
			if (Registry::exists('event')){
				$dispatcher = Registry::get('event');
				if (is_string($dispatcher)){
					$dispatcher = new $dispatcher();
				}
			}else{
				$dispatcher = new Dispatcher();
			}
			DI::set('event_dispatcher',$dispatcher);
		}
		return DI::get('event_dispatcher');
	}
	/**
	 * Listner
	 * @param string $name
	 * @param mixed $func
	 * @param integer $sort
	 * @return mixed
	 */
	public function on($name,$func,$sort = 500)
	{
		return $this->getDispatcher()->listen($name,$func,$sort);
	}
	/**
	 * Call event
	 * @param string $name
	 * @param array $params
	 * @param boolean $halt
	 * @return mixed
	 */
	public function fire($name,$params,$halt = true)
	{
		return $this->getDispatcher()->fire($name,$params,$halt);
	}
}