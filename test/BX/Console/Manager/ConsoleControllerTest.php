<?php namespace BX\Console\Manager;

class ConsoleControllerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return ConsoleController
	 */
	private function console()
	{
		return ConsoleController::getManager();
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