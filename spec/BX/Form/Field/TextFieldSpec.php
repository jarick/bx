<?php namespace spec\BX\Form\Field;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TextFieldSpec extends ObjectBehavior
{
	use \BX\Translate\TranslateTrait;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Form\Field\TextField');
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
	function it_validator()
	{
		$data = [];
		$this->translator()->addArrayResource(['validator.collection.string.min' => '#LABEL# IS MIN']);
		$this->setValue('asd');
		$this->getWrappedObject()->setValidator(function($validator){
			$validator->setMin(5);
		});
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS MIN']);
	}
	function it_min()
	{
		$data = [];
		$this->translator()->addArrayResource(['validator.collection.string.min' => '#LABEL# IS MIN']);
		$this->setValue('asd');
		$this->setMin(5);
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS MIN']);
	}
	function it_max()
	{
		$data = [];
		$this->translator()->addArrayResource(['validator.collection.string.max' => '#LABEL# IS MAX']);
		$this->setMax(3);
		$this->setValue('asds');
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS MAX']);
	}
	function it_correct()
	{
		$data = [];
		$this->setValue('asds');
		if (!$this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
	}
}