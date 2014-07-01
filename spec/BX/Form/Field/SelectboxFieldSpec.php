<?php namespace spec\BX\Form\Field;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SelectboxFieldSpec extends ObjectBehavior
{
	use \BX\Translate\TranslateTrait;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Form\Field\SelectboxField');
	}
	function let()
	{
		$enums = [
			'0'	 => 'None',
			'1'	 => 'First',
			'2'	 => 'Second',
			'3'	 => 'Third',
		];
		$this->beConstructedWith('TEST');
		$this->setEnums($enums);
		$this->setName('TEST')->setTabindex(1);
	}
	function it_uncorrect()
	{
		$data = [];
		$this->setValue('asdfgh');
		$this->translator()->addArrayResource(['form.field.select.invalid' => '#LABEL# IS INVALID']);
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS INVALID']);
	}
	function it_correct()
	{
		$data = [];
		$this->setValue('2');
		if (!$this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
	}
}