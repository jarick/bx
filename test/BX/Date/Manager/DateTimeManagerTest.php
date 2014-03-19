<?php namespace BX\Date\Manager;

class DateTimeManagerTest extends \BX_Test
{
	/**
	 * @var DateTimeManager
	 */
	private $date;
	public function setUp()
	{
		$this->date = DateTimeManager::getManager();
	}
	public function testCheckDateTime()
	{
		$this->assertTrue($this->date->checkDateTime('17.03.2014 12:00:00','d.m.Y H:i:s'));
		$this->assertFalse($this->date->checkDateTime('2014.03.17 122:00:00','d.m.Y H:i:s'));
	}
	public function testMakeTimeStamp()
	{
		$date = $this->date->makeTimeStamp('17.03.2014 12:00:00','d.m.Y H:i:s');
		$this->assertEquals($date,1395043200);
	}
}