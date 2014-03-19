<?php
use BX\Validator\Manager\Validator;
use BX\Validator\Manager\Custom;
use BX\Registry;

class CustomValidatorTest extends PHPUnit_Framework_TestCase
{
	public function returnTrue($value)
	{
		$this->assertEquals($value,'TEST');
		return true;
	}
	
	public function testTrue()
	{
		$validator = Validator::getManager(false,[
			'rules' => [
				['TEST',Custom::create([$this,'returnTrue'],'TEST ERROR')],
			],
			'labels' => ['TEST' => 'TEST'],
			'new' => true,
		]);
		$aFields = ['TEST' => 'TEST'];
		$this->assertTrue($validator->check($aFields));
	}

	public function returnFalse($value)
	{
		$this->assertEquals($value,'TEST');
		return false;
	}

	public function testMessage()
	{
		$validator = Validator::getManager(false,[
			'rules' => [
				['TEST',Custom::create([$this,'returnFalse'],'TEST ERROR')],
			],
			'labels' => ['TEST' => 'TEST'],
			'new' => true,
		]);
		$aFields = ['TEST' => 'TEST'];
		$this->assertFalse($validator->check($aFields));
		$this->assertEquals($validator->getErrors(), ['TEST' => ['TEST ERROR']]);
	}
}