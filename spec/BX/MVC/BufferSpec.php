<?php namespace spec\BX\MVC;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BufferSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\MVC\Buffer');
	}
	public function it_start()
	{
		$this->start();
		echo 'test';
		$this->end()->shouldBe('test');
	}
	public function it_flush()
	{
		$this->start();
		echo 'test';
		echo 'test';
		$this->flush();
		$this->start();
		echo 'test';
		$this->end()->shouldBe('test');
	}
	public function it_abort()
	{
		$this->start();
		echo 'test';
		$this->start();
		echo '|test';
		$this->abort()->shouldBe('test|test');
		$this->getStack()->shouldBe(0);
	}
}