<?php namespace spec\BX\DB\Filter\Rule;
use BX\DB\Filter\SqlBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BooleanSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\Filter\Rule\Boolean');
	}
	function let(SqlBuilder $builder)
	{
		$this->beConstructedWith($builder);
		$builder->adaptor()->willReturn($this->db()->adaptor());
		$builder->getColumn(Argument::any())->willReturn('T.TEST');
	}
	function it_true()
	{
		$this->addCondition('TEST','Y')->shouldReturn("T.TEST = 1");
	}
	function it_false()
	{
		$this->addCondition('TEST','N')->shouldReturn("T.TEST = 0");
	}
	function it_null()
	{
		$this->addCondition('TEST',false)->shouldReturn("(T.TEST IS NULL OR LENGTH(T.TEST)=0)");
	}
}