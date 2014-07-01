<?php namespace spec\BX\Validator;
use BX\DB\Test\TestTable;
use BX\Validator\Collection\StringValidator;
use PhpSpec\ObjectBehavior;

class LazyValueSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Validator\LazyValue');
	}
	function let()
	{
		$this->beConstructedWith(new TestTable());
	}
	function it_test()
	{
		$fields = ['TEST' => $this];
		$labels = ['ID' => 'ID','TEST' => 'TEST'];
		$key = 'TEST';
		$this->setParameters($key,$fields,$labels);
		$this->add(StringValidator::create()->notEmpty()->setMin(5)->setMessageMin('IS MIN'));
		$this->check('TEST')->shouldBe(false);
		$this->getEntity()->getErrors()->get('TEST')->shouldBe(['IS MIN']);
		$this->check('TEST5')->shouldBe(true);
	}
}