<?php
use BX\Validator\Manager\Validator;
use BX\Validator\Manager\DateTime;

class DateValidatorTest extends PHPUnit_Framework_TestCase
{
	private $validator;
	public function setUp()
	{
		$aLang = [
			'validator.manager.date.invalid' => '#LABEL# INVALID',
			'validator.manager.date.empty'	 => '#LABEL# EMPTY',
			'validator.manager.date.min'	 => '#LABEL# MIN #MIN#',
			'validator.manager.date.max'	 => '#LABEL# MAX #MAX#',
		];
		$this->validator = Validator::getManager(false,
										   [
				'rules'	 => [
					['TEST',DateTime::create()->notEmpty()],
				],
				'labels' => ['TEST' => 'TEST'],
				'new'	 => true,
		]);
		$this->validator->translator()->addArrayResource($aLang);
	}
	public function testTrue()
	{
		$fields = ['TEST' => '21.01.2012'];
		$this->assertTrue($this->validator->check($fields));
	}
	public function testInvalid()
	{
		$fields = ['TEST' => '12345'];
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(),
					  ['TEST' => ['TEST INVALID']]);
	}
	public function testEmpty()
	{
		$fields = ['TEST' => ''];
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(), ['TEST' => ['TEST EMPTY']]);
	}
	public function testMin()
	{
		$fields = ['TEST' => '20.01.2012'];
		$this->validator->setRules([
			['TEST',DateTime::create()->setMin('21.01.2012')],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(),
					  ['TEST' => ['TEST MIN 21.01.2012']]);
	}
	public function testMax()
	{
		$fields = ['TEST' => '22.01.2012'];
		$this->validator->setRules([
			['TEST',DateTime::create()->setMax('21.01.2012')],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(),
					  ['TEST' => ['TEST MAX 21.01.2012']]);
	}
}