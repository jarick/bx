<?php namespace spec\BX\Form\Field;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SessidFieldSpec extends ObjectBehavior
{
	use \BX\Http\HttpTrait,
	 \BX\Translate\TranslateTrait;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Form\Field\SessidField');
	}
	function it_validate()
	{
		$data = [];
		$this->setValue($this->session()->getId());
		if (!$this->getWrappedObject()->validate($data)){
			throw new \RuntimeException('Test falled');
		}
	}
}