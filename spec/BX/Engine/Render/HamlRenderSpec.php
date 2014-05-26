<?php namespace spec\BX\Engine\Render;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

class HamlRenderSpec extends ObjectBehavior
{
	use \BX\FileSystem\FileSystemTrait,
	 \BX\Config\ConfigTrait;
	private $store;
	function let()
	{
		$this->store = $this->config()->all();
		$store = [
			'templating' => [
				'engine'	 => 'php',
				'php'		 => __DIR__.'/data',
				'haml'		 => __DIR__.'/data',
				'doc_root'	 => __DIR__.'/data',
			],
		];
		$this->config()->init('array',$store);
	}
	function letgo()
	{
		$this->config()->init('array',$this->store);
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Engine\Render\HamlRender');
	}
	function it_render()
	{
		$dir = __DIR__.'/data/css';
		if (is_dir($dir)){
			$this->filesystem()->removePathDir($dir);
		}
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