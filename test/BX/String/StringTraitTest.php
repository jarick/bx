<?php namespace BX\String;

class StringTraitTest extends \BX\Test
{
	use StringTrait;
	public function test()
	{
		$this->assertInstanceOf('BX\String\StringManager',$this->string());
	}
}