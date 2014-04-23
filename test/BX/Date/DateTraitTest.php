<?php namespace BX\Date;
use BX\Test;

class DateTraitTest extends Test
{
	use DateTrait;
	public function test()
	{
		$this->assertInstanceOf("BX\Date\DateTimeManager",$this->date());
	}
}