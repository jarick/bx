<?php namespace BX\User\Store;
use BX\DB\ITable;
use BX\DB\UnitOfWork\Repository;
use BX\User\Entity\UserGroupMemberEntity;

class TableUserGroupMemberStore implements ITable
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
			'db_table'	 => 'tbl_user_group_member',
			'event'		 => 'UserGroupMember',
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
			UserGroupMemberEntity::C_ID			 => $this->column()->int('T.ID'),
			UserGroupMemberEntity::C_USER_ID	 => $this->column()->int('T.USER_ID'),
			UserGroupMemberEntity::C_GROUP_ID	 => $this->column()->int('T.GROUP_ID'),
			UserGroupMemberEntity::C_TIMESTAMP_X => $this->column()->datetime('T.TIMESTAMP_X'),
		];
	}
	/**
	 * Get finder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function getFinder()
	{
		return static::finder(UserGroupMemberEntity::getClass());
	}
	/**
	 * Add user group member
	 *
	 * @param Repository $repo
	 * @param UserGroupMemberEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function add(Repository $repo,UserGroupMemberEntity $entity)
	{
		$repo->add($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error add user group member. Error: {$mess}.");
		}
		return $entity->id;
	}
	/**
	 * Delete user group member
	 *
	 * @param Repository $repo
	 * @param UserGroupMemberEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function delete(Repository $repo,UserGroupMemberEntity $entity)
	{
		$repo->delete($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete user group member. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Delete all user group member by list
	 *
	 * @param Repository $repo
	 * @param UserGroupMemberEntity[] $entities
	 * @return boolean
	 */
	public function deleteAll(Repository $repo,$entities)
	{
		foreach($entities as $entity){
			$repo->delete($this,$entity);
		}
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete user group members. Error: {$mess}.");
		}
		return true;
	}
}