<?php namespace spec\BX\Mutex\Adaptor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SystemMutexSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Mutex\Adaptor\SystemMutex');
	}
	function it_acquire()
	{
		$entity = new \BX\Mutex\Entity\MutexEntity();
		$entity->generate('test');
		$this->acquire($entity)->shouldBe(true);
		$this->release($entity)->shouldBe(true);
	}
}