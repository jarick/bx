<?php namespace BX\Translate;

class TranslateTraitTest extends \BX\Test
{
	use TranslateTrait;
	public function test()
	{
		$this->assertInstanceOf('BX\Translate\TranslateManager',$this->translator());
	}
}