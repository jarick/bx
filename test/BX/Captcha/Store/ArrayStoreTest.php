<?php namespace BX\Captcha\Store;
use BX\Test;

class ArrayStoreTest extends Test
{
	private $store;
	public function setUp()
	{
		$save = new ArrayStore('121.0.0.1');
		$save->sid = '12345';
		$save->code = '54321';
		$this->store = new ArrayStore('121.0.0.1');
		$this->setPropertyValue($this->store,'data',[$save]);
	}
	public function testCurrent()
	{
		$captcha = $this->store->current('12345');
		$this->assertEquals('54321',$captcha->getCode());
		$this->assertEquals('121.0.0.1',$captcha->getUniqueId());
	}
	public function testSave()
	{
		$captcha = $this->store->save('qwerty');
		$this->assertEquals('qwerty',$captcha->getCode());
		$this->assertEquals('121.0.0.1',$captcha->getUniqueId());
	}
	public function testDelete()
	{
		$this->store->delete('12345');
		$this->assertNull($this->store->current('12345'));
	}
}