<?php namespace spec\BX\DB\Filter\Rule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use BX\DB\Filter\SqlBuilder;

class StringSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait;
	private $string = 121;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\Filter\Rule\String');
	}
	function let(SqlBuilder $builder)
	{
		$this->beConstructedWith($builder);
		$builder->adaptor()->willReturn($this->db()->adaptor());
		$builder->getColumn(Argument::any())->willReturn('T.TEST');
		$builder->bindParam('TEST',$this->string)->willReturn(':test_0');
	}
	function it_eq()
	{
		$this->addCondition('=TEST',$this->string)->shouldReturn("T.TEST = :test_0");
	}
	function it_like()
	{
		$this->addCondition('TEST',$this->string)->shouldReturn("T.TEST LIKE :test_0");
	}
	function it_sub_like(SqlBuilder $builder)
	{
		$builder->bindParam('TEST','%'.$this->string.'%')->willReturn(':test_0');
		$this->addCondition('%TEST',$this->string)->shouldReturn("UPPER(T.TEST) LIKE UPPER(:test_0)");
	}
	function it_null()
	{
		$this->addCondition('TEST',false)->shouldReturn("(T.TEST IS NULL OR LENGTH(T.TEST)=0)");
	}
}