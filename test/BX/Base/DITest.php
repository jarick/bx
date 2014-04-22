<?php namespace BX\Base;
use \BX\Base\DI;

class DITest extends \BX\Test
{
	public function test()
	{
		DI::set('test','test');
		$this->assertEquals('test',DI::get('test'));
	}
}