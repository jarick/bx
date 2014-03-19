<?php namespace BX\Date;

class DateTraitTest extends \BX_Test
{
	use DateTrait;
	public function test()
	{
		$this->assertInstanceOf("BX\Date\Manager\DateTimeManager",$this->date());
	}
}