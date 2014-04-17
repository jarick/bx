<?php namespace spec\BX\Engine\Render;
use BX\Base\Registry;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

class PhpRenderSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Engine\Render\PhpRender');
	}
	function let()
	{
		$store = [
			'templating' => [
				'engine' => 'php',
				'php'	 => __DIR__.'/data',
			],
		];
		Registry::init($store,Registry::FORMAT_ARRAY);
	}
	function it_render()
	{
		ob_start();
		$this->render([],'index',['a' => 'A'])->shouldBe(true);
		$content = ob_get_contents();
		ob_end_clean();
		if ($content !== '12345 TEST A'){
			throw new FailureException("Bad content: $content");
		}
	}
}