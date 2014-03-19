<?php
use BX\Event\Manager\EventManager;

class EventTest extends PHPUnit_Framework_TestCase
{
	public function event($param)
	{
		$this->assertTrue($param);
	}
	
	public function test()
	{
		$oEvent = EventManager::getManager();
		$oEvent->on('test.fire',function($param){
			$this->assertTrue($param);
			return false;
		});
		$this->assertFalse($oEvent->fire('test.fire', [true]));
		
		$oEvent->on('test.fire2',array($this,'event'));
		$this->assertNull($oEvent->fire('test.fire2', [true]));
	}
}