<?php namespace BX\Cache;

class CacheTraitClass extends \PHPUnit_Framework_TestCase
{
	use CacheTrait;
	public function testCache()
	{
		$this->assertInstanceOf("BX\Cache\Manager\Cache",$this->cache());
	}
}