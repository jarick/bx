<?php namespace BX\Date;
use BX\Test;

class DateTimeManagerTest extends Test
{
	/**
	 * @var \BX\Date\DateTimeManager
	 */
	private $date;
	public function setUp()
	{
		$this->date = new DateTimeManager();
	}
	public function testCheckDateTime()
	{
		$this->assertTrue($this->date->checkDateTime('17.03.2014 12:00:00','d.m.Y H:i:s'));
		$this->assertFalse($this->date->checkDateTime('2014.03.17 122:00:00','d.m.Y H:i:s'));
	}
	public function testMakeTimeStamp()
	{
		$date = $this->date->makeTimeStamp('17.03.2014 11:00:00','d.m.Y H:i:s');
		$this->assertEquals(1395043200,$date);
	}
	public function testConvertTimeStamp()
	{
		$date = $this->date->convertTimeStamp(1395043200,'d.m.Y H:i:s');
		$this->assertEquals('17.03.2014 11:00:00',$date);
	}
}