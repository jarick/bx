<?php namespace spec\BX\Mutex\Entity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MutexEntitySpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Mutex\Entity\MutexEntity');
	}
	public function it_test()
	{
		$this->generate('test');
		$this->key->shouldBe('3632233996');
		$this->max_acquire->shouldBeLike(1);
		$this->permission->shouldBeLike(0666);
	}
}