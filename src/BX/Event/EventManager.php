<?php namespace BX\Event;
use Illuminate\Events\Dispatcher;
use BX\Config\DICService;

class EventManager implements IEventManager
{
	use \BX\Config\ConfigTrait;
	/**
	 * Get dispatcher
	 * @return Dispatcher
	 */
	private function getDispatcher()
	{
		$name = 'event_dispatcher';
		if (DICService::get($name) === null){
			$manager = function(){
				if ($this->config()->exists('event','dispatcher')){
					$dispatcher = $this->config()->get('event','dispatcher');
					$dispatcher = new $dispatcher();
				}else{
					$dispatcher = new Dispatcher();
				}
				return $dispatcher;
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
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