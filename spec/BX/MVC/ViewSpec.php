<?php namespace spec\BX\MVC;
use BX\Base\DI;
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
		$engine->render($this->getWrappedObject(),'index',[])
			->will(function(){
				echo '123456 TEST A';
				return true;
			})->shouldBeCalled();
		DI::set('render',$engine->getWrappedObject());
		$this->render('index')->shouldBe('123456 TEST A');
		DI::set('render',null);
	}
}