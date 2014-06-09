<?php namespace BX\User\Store;
use BX\DB\ITable;
use BX\DB\UnitOfWork\Repository;
use BX\User\Entity\UserGroupEntity;

class TableUserGroupStore implements ITable
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
			'db_table'	 => 'tbl_user_group',
			'cache_tag'	 => 'UserGroup',
			'event'		 => 'UserGroup',
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
			UserGroupEntity::C_ID			 => $this->column()->int('T.ID'),
			UserGroupEntity::C_GUID			 => $this->column()->string('T.GUID'),
			UserGroupEntity::C_ACTIVE		 => $this->column()->bool('T.ACTIVE'),
			UserGroupEntity::C_TIMESTAMP_X	 => $this->column()->datetime('T.TIMESTAMP_X'),
			UserGroupEntity::C_NAME			 => $this->column()->string('T.NAME'),
			UserGroupEntity::C_DESCRIPTION	 => $this->column()->string('T.DESCRIPTION'),
			UserGroupEntity::C_SORT			 => $this->column()->int('T.SORT'),
		];
	}
	/**
	 * Get finder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function getFinder()
	{
		return static::finder(UserGroupEntity::getClass());
	}
	/**
	 * Add user group
	 *
	 * @param Repository $repo
	 * @param UserGroupEntity $entity
	 * @return integer
	 * @throws \RuntimeException
	 */
	public function add(Repository $repo,UserGroupEntity $entity)
	{
		$repo->add($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error add user group. Error: {$mess}.");
		}
		return $entity->id;
	}
	/**
	 * Update user group
	 *
	 * @param Repository $repo
	 * @param UserGroupEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function update(Repository $repo,UserGroupEntity $entity)
	{
		$repo->update($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error update user group. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Delete user group
	 *
	 * @param Repository $repo
	 * @param UserGroupEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function delete(Repository $repo,UserGroupEntity $entity)
	{
		$repo->delete($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete user group. Error: {$mess}.");
		}
		return true;
	}
}