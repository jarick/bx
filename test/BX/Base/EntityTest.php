<?php namespace BX\Base;
use \BX\Validator\Collection\String;

Class MockEntityTest
{
	use \BX\Validator\EntityTrait;
	protected function labels()
	{
		return [
			'TEST' => 'TEST',
		];
	}
	protected function rules()
	{
		return [
			['TEST'],
			String::create()->notEmpty(),
		];
	}
}

class EntityTest extends \BX\Test
{
	/**
	 * @var Entity
	 */
	protected $entity;
	public function setUp()
	{
		$this->entity = new MockEntityTest();
		$this->entity->test = 'TEST';
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
		$this->assertTrue($this->entity->hasErrors());
	}
	public function testAddError()
	{
		$this->entity->addError('unknow','unknow');
		$this->assertEquals($this->entity->getErrors()->get('UNKNOW'),['unknow']);
	}
}