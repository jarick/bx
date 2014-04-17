<?php namespace spec\BX\Engine;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EngineMock
{
	use \BX\Engine\EngineTrait;
}

class EngineTraitSpec extends ObjectBehavior
{
	function let()
	{
		$this->beAnInstanceOf('spec\BX\Engine\EngineMock');
	}
	function it_test()
	{
		$this->engine()->shouldHaveType('BX\Engine\EngineManager');
	}
}