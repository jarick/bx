<?php namespace BX\Validator\Manager;
use BX\Validator\Manager\Validator;
use BX\Validator\Manager\Number;

class NumberTest extends \BX_Test
{
	private $validator;
	public function setUp()
	{
		$aLang = [
			'validator.manager.number.invalid'	 => '#LABEL# INVALID',
			'validator.manager.number.empty'	 => '#LABEL# EMPTY',
			'validator.manager.number.min'		 => '#LABEL# MIN #MIN#',
			'validator.manager.number.max'		 => '#LABEL# MAX #MAX#',
			'validator.manager.number.integer'	 => '#LABEL# INTEGER',
		];
		$this->validator = Validator::getManager(false,[
				'rules'	 => [
					['TEST',Number::create()->notEmpty()],
				],
				'labels' => ['TEST' => 'TEST'],
				'new'	 => true,
		]);
		$this->validator->translator()->addArrayResource($aLang);
	}
	public function testTrue()
	{
		$fields = ['TEST' => 12345];
		$this->assertTrue($this->validator->check($fields));
	}
	public function testInvalid()
	{
		$fields = ['TEST' => ['12345']];
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST INVALID']]);
	}
	public function testEmpty()
	{
		$fields = ['TEST' => ''];
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST EMPTY']]);
	}
	public function testInteger()
	{
		$fields = ['TEST' => '12345.12'];
		$this->validator->setRules([
			['TEST',Number::create()->integer()],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST INTEGER']]);
	}
	public function testMin()
	{
		$fields = ['TEST' => 3];
		$this->validator->setRules([
			['TEST',Number::create()->setMin(4)],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST MIN 4']]);
	}
	public function testMax()
	{
		$fields = ['TEST' => 5];
		$this->validator->setRules([
			['TEST',Number::create()->setMax(4)],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST MAX 4']]);
	}
}