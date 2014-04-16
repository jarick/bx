<?php namespace spec\BX\DB\Filter\Rule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use BX\DB\Filter\SqlBuilder;

class DateTimeSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait,
	 \BX\Date\DateTrait;
	private $date = '21.02.2001 10:00:00';
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\Filter\Rule\DateTime');
	}
	function let(SqlBuilder $builder)
	{
		$this->beConstructedWith($builder);
		$builder->adaptor()->willReturn($this->db()->adaptor());
		$builder->getColumn(Argument::any())->willReturn('T.TEST');
		$time = strtotime($this->date) + $this->date()->getOffset();
		$builder->bindParam('TEST',$time)->willReturn(':test_0');
		$this->setFormat('full');
	}
	function it_more()
	{
		$this->addCondition('>TEST',$this->date)->shouldReturn("T.TEST > :test_0");
	}
	function it_less()
	{
		$this->addCondition('<TEST',$this->date)->shouldReturn("T.TEST < :test_0");
	}
	function it_more2()
	{
		$this->addCondition('>=TEST',$this->date)->shouldReturn("T.TEST >= :test_0");
	}
	function it_less2()
	{
		$this->addCondition('<=TEST',$this->date)->shouldReturn("T.TEST <= :test_0");
	}
	function it_null()
	{
		$this->addCondition('TEST',false)->shouldReturn("(T.TEST IS NULL OR LENGTH(T.TEST)=0)");
	}
}