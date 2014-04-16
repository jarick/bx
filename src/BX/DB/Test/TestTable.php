<?php namespace BX\DB\Test;
use BX\Validator\IEntity;

/**
 * @property-read integer $id
 * @property string $test
 * @property-read integer $user_id
 * @property-read string $user_login
 */
class TestTable implements IEntity, \BX\DB\ITable
{
	use \BX\Validator\EntityTrait,
	 \BX\DB\TableTrait;
	const C_ID = 'ID';
	const C_TEST = 'TEST';
	const C_USER_ID = 'USER_ID';
	const C_USER_LOGIN = 'USER_LOGIN';
	public function settings()
	{
		return [
			'db_table'	 => 'tbl_test',
			'cache_tag'	 => 'test',
			'event'		 => 'test',
		];
	}
	public function labels()
	{
		return [
			self::C_ID			 => 'ID label',
			self::C_TEST		 => 'Test label',
			self::C_USER_ID		 => 'User id label',
			self::C_USER_LOGIN	 => 'User login label',
		];
	}
	protected function rules()
	{
		return [
			[self::C_TEST],
			$this->rule()->string()->notEmpty()->setDefault('TEST'),
		];
	}
	protected function columns()
	{
		return [
			self::C_ID			 => $this->column()->int('T.ID'),
			self::C_TEST		 => $this->column()->string('T.TEST'),
			self::C_USER_ID		 => $this->column()->int('U.ID'),
			self::C_USER_LOGIN	 => $this->column()->string('U.LOGIN'),
		];
	}
	protected function relations()
	{
		return [
			[self::C_USER_ID,self::C_USER_LOGIN],
			$this->relation()->left('tbl_user U','T.ID = U.ID'),
		];
	}
	protected function search()
	{
		return [
			$this->index()->text(self::C_TEST),
		];
	}
}