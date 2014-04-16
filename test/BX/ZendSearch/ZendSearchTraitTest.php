<?php namespace BX\ZendSearch;
use BX\Test;

class ZendSearchTestTrait extends Test
{
	use ZendSearchTrait;
	public function test()
	{
		$this->assertInstanceOf('BX\ZendSearch\ZendSearchManager',$this->zendsearch());
	}
}