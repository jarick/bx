<?php namespace spec\BX\Form\Field;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CheckboxFieldSpec extends ObjectBehavior
{
	use \BX\Translate\TranslateTrait;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Form\Field\CheckboxField');
	}
	function let()
	{
		$this->beConstructedWith('TEST');
		$this->setName('TEST')->setTabindex(1);
	}
	function it_invalid()
	{
		$data = [];
		$lang = ['validator.collection.boolean.invalid' => '#LABEL# IS INVALID'];
		$this->translator()->addArrayResource($lang);
		$this->setvalue('asdasdasd');
		if ($this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
		$this->getErrors()->shouldBe(['TEST IS INVALID']);
	}
	function it_correct()
	{
		$data = [];
		$this->setvalue('Y');
		if (!$this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
	}
}