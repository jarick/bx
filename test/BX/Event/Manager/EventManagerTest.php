<?php namespace BX\Event\Manager;
use BX\Event\Manager\EventManager;

class EventManagerTest extends \BX\Test
{
	public function event($param)
	{
		$this->assertTrue($param);
	}
	
	public function test()
	{
		$event = EventManager::getManager();
		$event->on('test.fire',function($param){
			$this->assertTrue($param);
			return false;
		});
		$this->assertFalse($event->fire('test.fire', [true]));
		
		$event->on('test.fire2',array($this,'event'));
		$this->assertNull($event->fire('test.fire2', [true]));
	}
}