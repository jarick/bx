<?php namespace BX\Validator\Manager;
use BX\Validator\Manager\Validator;
use BX\Validator\Manager\String;
use BX\Validator\Manager\Multy;

class MultyTest extends \BX_Test
{
	private $validator;
	public function setUp()
	{
		$aLang = [
			'validator.manager.multy.empty'	 => '#LABEL# EMPTY',
			'validator.manager.multy.min'	 => '#LABEL# MIN #MIN#',
			'validator.manager.multy.max'	 => '#LABEL# MAX #MAX#',
			'validator.manager.multy.length' => '#LABEL# IS #LENGTH#',
			'validator.manager.string.min'	 => '#LABEL# MIN #MIN#',
		];
		$this->validator = Validator::getManager(false,[
				'rules'	 => [
					['TEST',Multy::create(String::create()->notEmpty())->notEmpty()],
				],
				'labels' => ['TEST' => 'TEST'],
				'new'	 => true,
		]);
		$this->validator->translator()->addArrayResource($aLang);
	}
	public function testTrue()
	{
		$aFields = [
			'TEST' => ['qwerty','qwerty']
		];
		$this->assertTrue($this->validator->check($aFields));
	}
	public function testEmpty()
	{
		$aFields = ['TEST' => []];
		$this->assertFalse($this->validator->check($aFields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST EMPTY']]);
	}
	public function testMin()
	{
		$aFields = ['TEST' => ['1','2','3']];
		$this->validator->setRules([
			['TEST',Multy::create(String::create()->notEmpty())->setMin(4)],
		]);
		$this->assertFalse($this->validator->check($aFields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST MIN 4']]);
	}
	public function testMax()
	{
		$aFields = ['TEST' => ['1','2','3','4','5']];
		$this->validator->setRules([
			['TEST',Multy::create(String::create()->notEmpty())->setMax(4)],
		]);
		$this->assertFalse($this->validator->check($aFields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST MAX 4']]);
	}
	public function testIs()
	{
		$aFields = ['TEST' => ['1','2','3','4','5']];
		$this->validator->setRules([
			['TEST',Multy::create(String::create()->notEmpty())->setLength(4)],
		]);
		$this->assertFalse($this->validator->check($aFields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST IS 4']]);
	}
	public function testChild()
	{
		$aFields = ['TEST' => ['1','2']];
		$this->validator->setRules([
			['TEST',Multy::create(String::create()->notEmpty()->setMin(4))->notEmpty()],
		]);
		$this->assertFalse($this->validator->check($aFields));
		$this->assertEquals($this->validator->getErrors(),['TEST' => ['TEST MIN 4','TEST MIN 4']]);
	}
}