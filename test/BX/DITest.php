<?php namespace BX;

class DITest extends \PHPUnit_Framework_TestCase
{
	public function test()
	{
		DI::set('test','test');
		$this->assertEquals('test',DI::get('test'));
	}
}