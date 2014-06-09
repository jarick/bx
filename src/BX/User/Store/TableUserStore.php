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
	 * @param Repository $repo
	 * @param UserEntity $entity
	 * @return integer
	 * @throws \RuntimeException
	 */
	public function add(Repository $repo,UserEntity $entity)
	{
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
	 * @param Repository $repo
	 * @param UserEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function update(Repository $repo,UserEntity $entity)
	{
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
	 * @param Repository $repo
	 * @param UserEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function delete(Repository $repo,UserEntity $entity)
	{
		$repo->delete($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete user. Error: {$mess}.");
		}
		return true;
	}
}