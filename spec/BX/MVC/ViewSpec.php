<?php namespace spec\BX\MVC;
use BX\Config\DICService;
use BX\Engine\EngineManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ViewSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\MVC\View');
	}
	function it_render(EngineManager $engine)
	{
		$engine_ptr = function()use($engine){
			$engine->render($this->getWrappedObject(),'index',[])
				->will(function(){
					echo '123456 TEST A';
					return true;
				})->shouldBeCalled();
			return $engine->getWrappedObject();
		};
		DICService::update('render',$engine_ptr);
		$this->render('index')->shouldBe('123456 TEST A');
	}
	function letgo()
	{
		DICService::delete('render');
	}
}