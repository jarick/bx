<?php namespace BX\Cache;

class CacheTest extends \BX\Test
{
	use \BX\Cache\CacheTrait,
	 \BX\Config\ConfigTrait;
	private $unique_id = 'test';
	private $ns = 'test';
	private $value = 'value';
	private $tags = ['tag1','tag2','tag3'];
	private $reg;
	public function setUp()
	{
		$this->reg = $this->config()->all();
		$config = [
			'cache' => [
				'type' => 'array',
			]
		];
		$this->config()->init('array',$config);
		$this->cache()->flush();
	}
	public function tearDown()
	{
		$this->config()->init('array',$this->reg);
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