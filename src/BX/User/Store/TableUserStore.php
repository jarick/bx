<?php namespace BX\User\Store;
use BX\DB\ITable;
use BX\DB\UnitOfWork\Repository;
use BX\User\Entity\UserEntity;

class TableUserStore implements ITable
{
	use \BX\DB\TableTrait;
	/**
	 * Return settings
	 *
	 * @return array
	 */
	protected function settings()
	{
		return [
			'db_table'	 => 'tbl_user',
			'cache_tag'	 => 'User',
			'event'		 => 'User',
		];
	}
	/**
	 * Return columns
	 *
	 * @return array
	 */
	protected function columns()
	{
		return [
			UserEntity::C_ID			 => $this->column()->int('T.ID'),
			UserEntity::C_GUID			 => $this->column()->string('T.GUID'),
			UserEntity::C_LOGIN			 => $this->column()->string('T.LOGIN'),
			UserEntity::C_PASSWORD		 => $this->column()->string('T.PASSWORD'),
			UserEntity::C_EMAIL			 => $this->column()->string('T.EMAIL'),
			UserEntity::C_CODE			 => $this->column()->string('T.CODE'),
			UserEntity::C_CREATE_DATE	 => $this->column()->datetime('T.CREATE_DATE'),
			UserEntity::C_TIMESTAMP_X	 => $this->column()->datetime('T.TIMESTAMP_X'),
			UserEntity::C_DISPLAY_NAME	 => $this->column()->string('T.DISPLAY_NAME'),
			UserEntity::C_REGISTERED	 => $this->column()->bool('T.REGISTERED'),
			UserEntity::C_ACTIVE		 => $this->column()->bool('T.ACTIVE'),
		];
	}
	/**
	 * Return finder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function getFinder()
	{
		return static::finder(UserEntity::getClass());
	}
	/**
	 * Add user
	 *
	 * @param array $user
	 * @return integer
	 * @throws \RuntimeException
	 */
	public function add(array $user)
	{
		$entity = new UserEntity();
		$entity->setData($user);
		$repo = new Repository('user');
		$repo->add($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error add user. Error: {$mess}.");
		}
		return $entity->id;
	}
	/**
	 * Update user
	 *
	 * @param integer $id
	 * @param array $user
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function update($id,array $user)
	{
		$repo = new Repository('user');
		$entity = static::finder(UserEntity::getClass())->filter(['ID' => $id])->get();
		if ($entity === false){
			throw new \RuntimeException("Error user is not found.");
		}
		$entity->setData($user);
		$repo->update($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error update user. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Delete user
	 *
	 * @param integer $id
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function delete($id)
	{
		$repo = new Repository('user');
		$entity = static::finder(UserEntity::getClass())->filter(['ID' => $id])->get();
		if ($entity === false){
			throw new \RuntimeException("Error user is not found.");
		}
		$repo->delete($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete user. Error: {$mess}.");
		}
		return true;
	}
}