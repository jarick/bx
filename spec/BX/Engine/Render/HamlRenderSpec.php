<?php namespace spec\BX\Engine\Render;
use BX\Base\Registry;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

class HamlRenderSpec extends ObjectBehavior
{
	use \BX\FileSystem\FileSystemTrait;
	private $store;
	function let()
	{
		$this->store = Registry::all();
		$store = [
			'templating' => [
				'engine'	 => 'php',
				'php'		 => __DIR__.'/data',
				'haml'		 => __DIR__.'/data',
				'doc_root'	 => __DIR__.'/data',
			],
		];
		Registry::init($store,Registry::FORMAT_ARRAY);
	}
	function letgo()
	{
		Registry::init($this->store,Registry::FORMAT_ARRAY);
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Engine\Render\HamlRender');
	}
	function it_render()
	{
		$this->filesystem()->removePathDir(__DIR__.'/data/css');
		ob_start();
		$this->render([],'index',['a' => 'A'])->shouldBe(true);
		$content = ob_get_contents();
		ob_end_clean();
		if ($content !== '12345 TEST A'){
			throw new FailureException("Bad content: $content");
		}
		if (!file_exists(__DIR__.'/data/css/index.css')){
			throw new FailureException("Css is not generate");
		}
	}
}