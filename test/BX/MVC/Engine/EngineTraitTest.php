<?php namespace BX\Engine\Manager;

class EngineTraitTest extends \PHPUnit_Framework_TestCase
{
	use \BX\Engine\EngineTrait;
	public function testHaml()
	{
		\BX\Registry::init(['templating' => ['engine' => 'haml']],\BX\Registry::FORMAT_ARRAY);
		$this->assertInstanceOf('BX\Engine\Manager\HamlEngine',$this->engine());
	}
	public function testPhp()
	{
		\BX\Registry::init(['templating' => ['engine' => 'php']],\BX\Registry::FORMAT_ARRAY);
		$this->assertInstanceOf('BX\Engine\Manager\PhpEngine',$this->engine());
	}
}