<?php namespace BX\Console;
use BX\Console\ConsoleController;
use BX\Test;

class ConsoleControllerTest extends Test
{
	/**
	 * @return ConsoleController
	 */
	private function console()
	{
		return new ConsoleController();
	}
	public function testExec()
	{
		$controller = $this->console();
		$command = $this->getMock('BX\Console\Command\Console',['run','command'],[]);
		$command->expects($this->once())->method('run')->with($this->equalTo(['arg1','arg2']));
		$command->expects($this->any())->method('command')->will($this->returnValue('test'));
		$controller->command->attach($command);
		$controller->exec('test arg1 arg2');
	}
}