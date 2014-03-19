<?php namespace BX\Console;

class ConsoleTraitTest extends \PHPUnit_Framework_TestCase
{
	use ConsoleTrait;
	public function test()
	{
		$this->assertInstanceOf("BX\Console\Manager\ConsoleController",$this->console());
	}
}