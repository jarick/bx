<?php namespace BX\Cache;
use BX\Base\Registry;

class CacheTest extends \BX\Test
{
	use \BX\Cache\CacheTrait;
	private $unique_id = 'test';
	private $ns = 'test';
	private $value = 'value';
	private $tags = ['tag1','tag2','tag3'];
	public static function setUpBeforeClass()
	{
		Registry::init([
			'cache' => [
				'type' => 'array',
			]
			],Registry::FORMAT_ARRAY);
	}
	public function setUp()
	{
		$this->cache()->flush();
	}
	public function testSetTags()
	{
		$this->cache()->setTags($this->ns,$this->tags);
		$this->assertEquals($this->tags,$this->cache()->adaptor()->get($this->ns));
	}
	public function testGet()
	{
		$this->cache()->set($this->unique_id,$this->value,$this->ns);
		$this->assertEquals($this->value,$this->cache()->get($this->unique_id,$this->ns));
	}
	public function testRemove()
	{
		$this->cache()->set($this->unique_id,$this->value,$this->ns,3600,$this->tags);
		$this->cache()->remove($this->unique_id,$this->ns);
		$this->assertNull($this->cache()->get($this->unique_id,$this->ns));
	}
	public function testRemoveByNamespace()
	{
		$this->cache()->set($this->unique_id,$this->value,$this->ns,3600,$this->tags);
		$this->cache()->removeByNamespace($this->ns);
		$this->assertNull($this->cache()->get($this->unique_id,$this->ns));
	}
	public function testClearByTags()
	{
		$this->cache()->set($this->unique_id,$this->value,$this->ns,3600,$this->tags);
		$this->cache()->clearByTags($this->tags);
		$this->assertNull($this->cache()->get($this->unique_id,$this->ns));
	}
}