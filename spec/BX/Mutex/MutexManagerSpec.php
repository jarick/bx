<?php namespace spec\BX\Mutex;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MutexManagerSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Mutex\MutexManager');
	}
	function it_mutex()
	{
		$this->adaptor()->shouldHaveType('\BX\Mutex\Adaptor\IMutexAdaptor');
	}
	function if_test()
	{
		$this->acquire('test')->shouldBe(true);
		$this->release('test')->shouldBe(true);
	}
}