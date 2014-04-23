<?php namespace BX\Cache;

class CacheTraitClass extends \BX\Test
{
	use CacheTrait;
	public function testCache()
	{
		$this->assertInstanceOf("BX\Cache\CacheManager",$this->cache());
	}
}