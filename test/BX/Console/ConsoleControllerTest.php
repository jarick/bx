<?php namespace BX\Console;
use BX\Console\ConsoleController;

class ConsoleControllerTest extends \BX\Test
{
	public function testExec()
	{
		$controller = new ConsoleController();
		$command = $this->getMock('BX\Console\Command\Console',['run','command'],[]);
		$command->expects($this->once())->method('run')->with($this->equalTo(['arg1','arg2']));
		$command->expects($this->any())->method('command')->will($this->returnValue('test'));
		$controller->command->add($command);
		$controller->exec('test arg1 arg2');
	}
}