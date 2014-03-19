<?php
use BX\MVC\Buffer;

class BufferTest extends PHPUnit_Framework_TestCase
{
	public function testStart()
	{
		$buffer = new Buffer();
		$buffer->start();
		echo 'test';
		$this->assertEquals($buffer->end(),'test');
	}
	public function testFlush()
	{
		$buffer = new Buffer();
		$buffer->start();
		echo 'test';
		echo 'test';
		$buffer->flush();
		$buffer->start();
		echo 'test';
		$this->assertEquals($buffer->end(),'test');
	}
	public function testAbort()
	{
		$buffer = new Buffer();
		$buffer->start();
		echo 'test';
		$buffer->start();
		echo '|test';
		$this->assertEquals($buffer->abort(),'test|test');
		$this->assertEquals(PHPUnit_Framework_Assert::readAttribute($buffer,'stack'),0);
	}
}