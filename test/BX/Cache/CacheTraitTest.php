<?php namespace BX\Cache;
use BX\Test;

class CacheTraitClass extends Test
{
	use CacheTrait;
	public function testCache()
	{
		$this->assertInstanceOf("BX\Cache\CacheManager",$this->cache());
	}
}