<?php namespace spec\BX\Mutex;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MockMutexTrait
{
	use \BX\Mutex\MutexTrait;
}

class MutexTraitSpec extends ObjectBehavior
{
	function let()
	{
		$this->beAnInstanceOf('spec\BX\Mutex\MockMutexTrait');
	}
	public function it_test()
	{
		$this->mutex()->shouldHaveType('BX\Mutex\MutexManager');
	}
}