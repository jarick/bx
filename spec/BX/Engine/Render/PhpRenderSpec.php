<?php namespace spec\BX\Engine\Render;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

class PhpRenderSpec extends ObjectBehavior
{
	use \BX\Config\ConfigTrait;
	private $reg;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Engine\Render\PhpRender');
	}
	function let()
	{
		$this->reg = $this->config()->all();
		$store = [
			'templating' => [
				'engine' => 'php',
				'php'	 => __DIR__.'/data',
			],
		];
		$this->config()->init('array',$store);
	}
	function letgo()
	{
		$this->config()->init('array',$this->reg);
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