<?php namespace BX\Validator\Manager;
use BX\Validator\Manager\Validator;
use BX\Validator\Manager\String;
use BX\Registry;
use BX\Validator\Manager\Boolean;

class BooleanTest extends \BX\Test
{
	private $validator;
	public function setUp()
	{
		$aLang = [
			'validator.manager.boolean.invalid' => '#LABEL# INVALID',
		];
		$this->validator = Validator::getManager(false,[
				'labels' => ['TEST' => 'TEST'],
				'new'	 => true,
				'rules'	 => [
					['TEST',Boolean::create()->setValue(1,0)],
				]
		]);
		$this->validator->translator()->addArrayResource($aLang);
	}
	public function testStringTrue()
	{
		$fields = ['TEST' => '1'];
		$this->assertTrue($this->validator->check($fields));
	}
	public function testNotStrict()
	{
		$fields = ['TEST' => '2'];
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors()->get('TEST'),['TEST INVALID']);
	}
	public function testStrict()
	{
		$fields = ['TEST' => '1'];
		$this->validator->setRules([
			['TEST',Boolean::create()->setValue(1,0)->strict()],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors()->get('TEST'),['TEST INVALID']);
	}
}