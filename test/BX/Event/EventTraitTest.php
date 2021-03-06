<?php namespace BX\Event;

class EventTraitTest extends \BX\Test
{
	use EventTrait;
	public function listen($param)
	{
		$this->assertTrue($param);
	}
	public function test()
	{
		$this->on('test.fire',function($param){
			$this->assertTrue($param);
			return false;
		});
		$this->assertFalse($this->fire('test.fire',[true]));

		$this->on('test.fire2',array($this,'listen'));
		$this->assertNull($this->fire('test.fire2',[true]));
	}
}