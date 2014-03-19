<?php
namespace BX\Event\Manager;
use BX\Manager;
use BX\Event\IEvent;
use Illuminate\Events\Dispatcher;
use BX\Registry;
use BX\DI;

class EventManager extends Manager implements IEvent
{
	private function getDispatcher()
	{
		if(DI::get('event_dispatcher') === null){
			if(Registry::exists('event')){
				$dispatcher = Registry::get('event'); 
				if(is_string($dispatcher)){
					$dispatcher = new $dispatcher();
				}
			} else{
				$dispatcher = new Dispatcher();
			}
			DI::set('event_dispatcher',$dispatcher);
		}
		return DI::get('event_dispatcher');
	}
	
	public function on($name,$func,$sort = 500)
	{
		return $this->getDispatcher()->listen($name,$func,$sort);
	}
	
	public function fire($name,$params,$halt = true)
	{
		return $this->getDispatcher()->fire($name,$params,$halt);
	}
}