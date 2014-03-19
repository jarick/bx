<?php namespace BX;
use BX\Validator\Manager\String;

Class MockEntityTest extends Entity
{
	protected function labels()
	{
		return [
			'TEST' => 'TEST',
		];
	}
	protected function rules()
	{
		return [
			['TEST',String::create()->notEmpty()],
		];
	}
}

class EntityTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Entity
	 */
	protected $entity;
	public function setUp()
	{
		$this->entity = new MockEntityTest();
		$this->entity->test = 'TEST';
		$this->entity->resetErrors();
	}
	public function testGetLabel()
	{
		$this->assertEquals('TEST',$this->entity->getLabel('test'));
	}
	public function testGetValue()
	{
		$this->assertEquals('TEST',$this->entity->getValue('test'));
	}
	public function testSetValue()
	{
		$this->entity->setValue('TEST','TEST2');
		$this->assertEquals('TEST2',$this->entity->getValue('test'));
	}
	public function testGetData()
	{
		$this->assertEquals(['TEST' => 'TEST'],$this->entity->getData());
	}
	public function testSetData()
	{
		$this->entity->setData(['TEST' => 'TEST2']);
		$this->assertEquals(['TEST' => 'TEST2'],$this->entity->getData());
	}
	public function testCheckFields()
	{
		$data = ['TEST' => 'TEST'];
		$this->assertTrue($this->entity->checkFields($data));
		$data = ['TEST' => ''];
		$this->assertFalse($this->entity->checkFields($data));
	}
	public function testHasError()
	{
		$data = ['TEST' => ''];
		$this->assertFalse($this->entity->checkFields($data));
		$this->assertTrue($this->entity->hasError());
	}
	public function testAddError()
	{
		$this->entity->addError('unknow','unknow');
		$this->assertEquals($this->entity->getErrors(),['UNKNOW' => ['unknow']]);
	}
	public function testResetError()
	{
		$this->entity->addError('unknow','unknow');
		$this->entity->resetErrors();
		$this->assertFalse($this->entity->hasError());
	}
}