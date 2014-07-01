<?php namespace spec\BX\Form\Field;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateFieldSpec extends ObjectBehavior
{
	use \BX\Translate\TranslateTrait;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Form\Field\DateField');
	}
	function let()
	{
		$this->beConstructedWith('TEST');
		$this->setName('TEST')->setTabindex(1);
	}
	function it_required()
	{
		$data = [];
		$this->translator()->addArrayResource(['validator.manager.date.empty' => '#LABEL# IS EMPTY']);
		$this->required();
		$this->setValue('');
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS EMPTY']);
	}
	function it_invalid()
	{
		$data = [];
		$this->translator()->addArrayResource(['validator.manager.date.invalid' => '#LABEL# IS INVALID']);
		$this->setValue('asdasdasd');
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS INVALID']);
	}
	function it_min()
	{
		$data = [];
		$this->translator()->addArrayResource(['validator.manager.date.min' => '#LABEL# IS MIN']);
		$this->setValue('28.06.2014 21:00:00');
		$this->setMin('29.06.2014');
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS MIN']);
	}
	function it_max()
	{
		$data = [];
		$this->translator()->addArrayResource(['validator.manager.date.max' => '#LABEL# IS MAX']);
		$this->setMax('27.06.2014');
		$this->setValue('28.06.2014 22:00:00');
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS MAX']);
	}
}