<?php namespace BX\Validator\Manager;
use BX\Validator\Manager\Validator;
use BX\Validator\Manager\String;
use BX\Validator\Manager\Multy;

class MultyTest extends \BX\Test
{
	private $validator;
	public function setUp()
	{
		$aLang = [
			'validator.manager.multy.empty'	 => '#LABEL# EMPTY',
			'validator.manager.multy.min'	 => '#LABEL# MIN #MIN#',
			'validator.manager.multy.max'	 => '#LABEL# MAX #MAX#',
			'validator.manager.multy.length' => '#LABEL# IS #LENGTH#',
			'validator.manager.string.empty' => '#LABEL# EMPTY',
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
		$fields = [
			'TEST' => ['qwerty','qwerty']
		];
		$this->assertTrue($this->validator->check($fields));
	}
	public function testEmpty()
	{
		$fields = ['TEST' => []];
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors()->get('TEST'),['TEST EMPTY']);
	}
	public function testMin()
	{
		$fields = ['TEST' => ['1','2','3']];
		$this->validator->setRules([
			['TEST',Multy::create(String::create()->notEmpty())->setMin(4)],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors()->get('TEST'),['TEST MIN 4']);
	}
	public function testMax()
	{
		$fields = ['TEST' => ['1','2','3','4','5']];
		$this->validator->setRules([
			['TEST',Multy::create(String::create()->notEmpty())->setMax(4)],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors()->get('TEST'),['TEST MAX 4']);
	}
	public function testIs()
	{
		$fields = ['TEST' => ['1','2','3','4','5']];
		$this->validator->setRules([
			['TEST',Multy::create(String::create()->notEmpty())->setLength(4)],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors()->get('TEST'),['TEST IS 4']);
	}
	public function testChild()
	{
		$fields = ['TEST' => ['','2']];
		$this->validator->setRules([
			['TEST',Multy::create(String::create()->notEmpty()->setMin(4)->notEmpty())->notEmpty()],
		]);
		$this->assertFalse($this->validator->check($fields));
		$this->assertEquals($this->validator->getErrors()->get('TEST'),['TEST EMPTY','TEST MIN 4']);
	}
}