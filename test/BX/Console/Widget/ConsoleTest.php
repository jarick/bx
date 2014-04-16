<?php namespace BX\Console\Widget;
use \BX\Collection;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
	use \BX\Http\HttpTrait,
	 \BX\Event\EventTrait;
	public function testPostTrue()
	{
		$post = ['FORM' => ['CODE' => 'test arg1 arg2']];
		$this->request()->setPost($post);
		$view = $this->getMock('BX\MVC\View',['abort']);
		$view->init();
		$this->on(Console::EVENT_LIST_COMMAND,function(Collection $commands){
			$command = $this->getMock('BX\Console\Command\Console',['run','command'],[]);
			$command->expects($this->once())->method('run')->with($this->equalTo(['arg1','arg2']));
			$command->expects($this->any())->method('command')->will($this->returnValue('test'));
			$commands->attach($command);
		});
		$widget = $this->getMock('BX\Console\Widget\Console',['render']);
		$widget->setView($view);
		$widget->expects($this->once())->method('render')->will(
			$this->returnCallback(function($file,$params) use($post){
				$this->assertFalse($file);
				$this->assertEquals($post['FORM'],$params['post']);
				$this->assertFalse($params['validator']->hasErrors());
			})
		);
		$widget->run();
	}
	public function testPostFalse()
	{
		$post = ['FORM' => ['CODE' => '']];
		$this->request()->setPost($post);
		$view = $this->getMock('BX\MVC\Manager\View',['abort']);
		$view->init();
		$this->on(Console::EVENT_LIST_COMMAND,function(Collection $commands){
			$command = $this->getMock('BX\Console\Command\Console',['run','command'],[]);
			$command->expects($this->once())->method('run')->with($this->equalTo(['arg1','arg2']));
			$command->expects($this->any())->method('command')->will($this->returnValue('test'));
			$commands->attach($command);
		});
		$widget = $this->getMock('BX\Console\Widget\Console',['render']);
		$widget->setView($view);
		$widget->expects($this->once())->method('render')->will(
			$this->returnCallback(function($file,$params) use($post){
				$this->assertFalse($file);
				$this->assertEquals($post['FORM'],$params['post']);
				$this->assertTrue($params['validator']->hasErrors());
			})
		);
		$widget->run();
	}
}