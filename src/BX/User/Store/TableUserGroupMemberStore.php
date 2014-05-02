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
	 * @param array $group
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function add($user_id,$group_id)
	{
		$entity = new UserGroupMemberEntity();
		$entity->user_id = $user_id;
		$entity->group_id = $group_id;
		$repo = new Repository('user_group_member');
		$repo->appendLockTables(['tbl_user','tbl_user_group']);
		$repo->add($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error add user group member. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Delete user group
	 *
	 * @param integer $id
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function delete($user_id,$group_id)
	{
		$repo = new Repository('user_group_member');
		$repo->appendLockTables(['tbl_user','tbl_user_group']);
		$filter = [
			'USER_ID'	 => $user_id,
			'GROUP_ID'	 => $group_id,
		];
		$entity = $this->getFinder()->filter($filter)->get();
		if ($entity === false){
			throw new \RuntimeException("Error user group member is not found.");
		}
		$repo->delete($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete user group member. Error: {$mess}.");
		}
		return true;
	}
}