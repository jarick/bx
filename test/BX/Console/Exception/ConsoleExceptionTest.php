<?php namespace BX\Console\Exception;

class ConsoleExceptionTest extends \PHPUnit_Framework_TestCase
{
	private $message = 'message';
	private $file = 'file';
	private $line = 100;
	public function testRender()
	{
		$writer = $this->getMock('BX\Console\Manager\HtmlManager',['error']);
		$writer->expects($this->any())
			->method('error')
			->with($this->equalTo($this->message.' FILE: '.$this->file.' LINE: '.$this->line))
			->will($this->returnValue(''));
		$exception = new ConsoleException($this->message,$writer);
		$exception->setDebugInfo($this->file,$this->line);
		$exception->render();
	}
}