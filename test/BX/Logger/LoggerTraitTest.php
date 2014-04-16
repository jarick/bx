<?php namespace BX\Logger;
use BX\Test;

class LoggerTraitTest extends Test
{
	use LoggerTrait;
	public function test()
	{
		$this->assertInstanceOf('Monolog\Logger',$this->log());
	}
}