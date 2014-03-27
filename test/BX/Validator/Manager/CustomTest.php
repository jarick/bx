<?php namespace BX\Validator\Manager;
use BX\Validator\Manager\Validator;
use BX\Validator\Manager\Custom;
use BX\Registry;

class CustomTest extends \BX\Test
{
	public function returnTrue($value)
	{
		$this->assertEquals($value,'TEST');
		return true;
	}
	public function testTrue()
	{
		$validator = Validator::getManager(false,[
				'rules'	 => [
					['TEST',Custom::create([$this,'returnTrue'],'TEST ERROR')],
				],
				'labels' => ['TEST' => 'TEST'],
				'new'	 => true,
		]);
		$aFields = ['TEST' => 'TEST'];
		$this->assertTrue($validator->check($aFields));
	}
	public function returnFalse($value)
	{
		$this->assertEquals($value,'TEST');
		return 'TEST ERROR';
	}
	public function testMessage()
	{
		$validator = Validator::getManager(false,[
				'rules'	 => [
					['TEST',Custom::create([$this,'returnFalse'])],
				],
				'labels' => ['TEST' => 'TEST'],
				'new'	 => true,
		]);
		$aFields = ['TEST' => 'TEST'];
		$this->assertFalse($validator->check($aFields));
		$this->assertEquals($validator->getErrors()->get('TEST'), ['TEST ERROR']);
	}
}