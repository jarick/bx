<?php namespace BX\Translate;

class TranslateTraitTest extends \BX_Test
{
	use TranslateTrait;
	public function test()
	{
		$this->assertInstanceOf('BX\Translate\Manager\Translate',$this->translator());
	}
}