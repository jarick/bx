<?php namespace spec\BX\Form\Field;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NumberFieldS_pec extends ObjectBehavior
{
	use \BX\Translate\TranslateTrait;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Form\Field\NumberField');
	}
	function let()
	{
		$this->beConstructedWith('TEST');
		$this->setName('TEST')->setTabindex(1);
	}
	function it_required()
	{
		$data = [];
		$this->required();
		$this->setValue('');
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
	}
	function it_min()
	{
		$data = [];
		$this->translator()->addArrayResource(['form.field.number.min' => '#LABEL# IS MIN']);
		$this->setMin(10)->setValue('5');
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS MIN']);
	}
	function it_max()
	{
		$data = [];
		$this->translator()->addArrayResource(['form.field.number.max' => '#LABEL# IS MAX']);
		$this->setmax(5)->setValue('10');
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS MAX']);
	}
	function it_correct()
	{
		$data = [];
		$this->setValue('5');
		if (!$this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
	}
}