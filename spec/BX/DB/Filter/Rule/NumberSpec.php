<?php namespace spec\BX\DB\Filter\Rule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use BX\DB\Filter\SqlBuilder;

class NumberSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait,
	 \BX\Date\DateTrait;
	private $number = 121;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\Filter\Rule\Number');
	}
	function let(SqlBuilder $builder)
	{
		$this->beConstructedWith($builder);
		$builder->adaptor()->willReturn($this->db()->adaptor());
		$builder->getColumn(Argument::any())->willReturn('T.TEST');
		$builder->bindParam('TEST',$this->number)->willReturn(':test_0');
	}
	function it_more()
	{
		$this->addCondition('>TEST',$this->number)->shouldReturn("T.TEST > :test_0");
	}
	function it_less()
	{
		$this->addCondition('<TEST',$this->number)->shouldReturn("T.TEST < :test_0");
	}
	function it_more2()
	{
		$this->addCondition('>=TEST',$this->number)->shouldReturn("T.TEST >= :test_0");
	}
	function it_less2()
	{
		$this->addCondition('<=TEST',$this->number)->shouldReturn("T.TEST <= :test_0");
	}
	function it_null()
	{
		$this->addCondition('TEST',false)->shouldReturn("(T.TEST IS NULL OR LENGTH(T.TEST)=0)");
	}
}