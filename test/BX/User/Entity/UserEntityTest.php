<?php namespace BX\User\Entity;
use BX\DI;

class UserEntityTest extends \BX_Test
{
	use \BX\Translate\TranslateTrait;
	/**
	 * @var UserEntity
	 */
	private $entity;
	public static function setUpBeforeClass()
	{
		$db = __DIR__.DIRECTORY_SEPARATOR.'db.db';
		if (file_exists($db)){
			unlink($db);
		}
		$pdo = new \PDO("sqlite:{$db}");
		$pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
		DI::set('pdo',$pdo);
		$migrate = new \BX\User\Migration();
		$migrate->upCreateTable(true);
		$user = UserEntity::getEntity();
		$user->login = 'admin';
		$user->active = 'Y';
		$user->display_name = 'admin';
		$user->email = 'no@email.com';
		$user->registered = 'Y';
		$user->active = 'Y';
		$user->add();
	}
	public function setUp()
	{
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
	public function testLoginByLogin()
	{
		var_dump($this->entity->filter()->all());
	}
	public static function tearDownAfterClass()
	{
		$migrate = new \BX\User\Migration();
		$migrate->upCreateTable(false);
		DI::set('pdo',null);
	}
}