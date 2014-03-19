<?php
use BX\Validator\Manager\Validator;
use BX\Validator\Manager\String;
use BX\Registry;

class StringValidatorTest extends PHPUnit_Framework_TestCase
{
	private $validator;

	public function setUp()
	{
		$aLang = [
			'validator.manager.string.invalid' => '#LABEL# INVALID',
			'validator.manager.string.empty' => '#LABEL# EMPTY',
			'validator.manager.string.min' => '#LABEL# MIN #MIN#',
			'validator.manager.string.max' => '#LABEL# MAX #MAX#',
			'validator.manager.string.length' => '#LABEL# IS #LENGTH#',
		];
		$this->validator = Validator::getManager(false,[
			'rules' => [
				['TEST',String::create()->notEmpty()],
			],
			'labels' => ['TEST' => 'TEST'],
			'new' => true,
		]);
		$this->validator->translator()->addArrayResource($aLang);
	}

	public function testTrue()
	{
		$fields = [
			'TEST'=>'qwerty',
		];
		$this->assertTrue($this->validator->check($fields));
	}

	public function testInvalid()
	{
		$fields = [
			'TEST'=>[''],
		];
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(), ['TEST' => ['TEST INVALID']]);
	}

	public function testEmpty()
	{
		$fields = ['TEST'=>''];
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(), ['TEST' => ['TEST EMPTY']]);
	}

	public function testMin()
	{
		$fields = ['TEST'=>'QWE'];
		$this->validator->setRules([
			['TEST',String::create()->notEmpty()->setMin(4)],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(), ['TEST' => ['TEST MIN 4']]);
	}

	public function testMax()
	{
		$fields = ['TEST'=>'QWERTY'];
		$this->validator->setRules([
			['TEST',String::create()->notEmpty()->setMax(4)],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(), ['TEST' => ['TEST MAX 4']]);
	}

	public function testIs()
	{
		$fields = ['TEST'=>'QWERTY'];
		$this->validator->setRules([
			['TEST',String::create()->notEmpty()->setLength(4)],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(), ['TEST' => ['TEST IS 4']]);
	}
}