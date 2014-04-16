<?php namespace BX\User\Entity;
use BX\DBTest;

class UserEntityTest extends DBTest
{
	use \BX\Translate\TranslateTrait;
	/**
	 * @var UserEntity
	 */
	private $entity;
	public function setUp()
	{
		parent::setUp();
		$this->entity = UserEntity::getEntity();
	}
	public function testValidatePasswordEmpty()
	{
		$value = '';
		$this->translator()->addArrayResource(['user.entity.user.error_password_empty' => 'TEST EMPTY']);
		$return = $this->entity->validatePassword($value);
		$this->assertEquals('TEST EMPTY',$return);
	}
	public function testValidatePasswordMin()
	{
		$value = '12345';
		$this->translator()->addArrayResource(['user.entity.user.error_password_min' => 'TEST MIN #MIN#']);
		$return = $this->entity->validatePassword($value);
		$this->assertEquals('TEST MIN 6',$return);
	}
	public function testValidatePassword()
	{
		$value = '123456';
		$return = $this->entity->validatePassword($value);
		$this->assertEmpty($return);
		$this->assertTrue(password_verify('123456',$value));
	}
}