<?php namespace BX\User;
use BX\User\Store\TableUserGroupMemberStore;
use BX\User\Entity\UserGroupMemberEntity;

class UserGroupMemberManager
{
	use \BX\Config\ConfigTrait;
	/**
	 * @var TableUserGroupMemberStore
	 */
	private $store;
	/**
	 * Return store
	 *
	 * @return TableUserGroupMemberStore
	 * @throws \RuntimeException
	 */
	private function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('user','group_member','store')){
				$store = $this->config()->get('user','group_member','store');
				switch ($store){
					case 'db': $this->store = new TableUserGroupMemberStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableUserGroupMemberStore();
			}
		}
		return $this->store;
	}
	/**
	 * Add user group member
	 *
	 * @param integer $user_id
	 * @param integer $group_id
	 * @return boolean
	 */
	public function add($user_id,$group_id)
	{
		$entity = new UserGroupMemberEntity();
		$entity->user_id = $user_id;
		$entity->group_id = $group_id;
		$repo = $this->store()->getRepository('user_group_member');
		$repo->appendLockTables(['tbl_user','tbl_user_group']);
		return $this->store()->add($repo,$entity);
	}
	/**
	 * Delete user group member
	 *
	 * @param integer $user_id
	 * @param integer $group_id
	 * @return boolean
	 */
	public function delete($user_id,$group_id)
	{
		$repo = $this->store()->getRepository('user_group_member');
		$repo->appendLockTables(['tbl_user','tbl_user_group']);
		$filter = [
			UserGroupMemberEntity::C_USER_ID	 => $user_id,
			UserGroupMemberEntity::C_GROUP_ID	 => $group_id,
		];
		$entity = $this->finder()->filter($filter)->get();
		if ($entity === false){
			throw new \RuntimeException("Error user group member is not found.");
		}
		return $this->store()->delete($repo,$entity);
	}
	/**
	 * Delete all members by user id
	 *
	 * @param integer $user_id
	 * @return boolean
	 */
	public function deleteAllByUserId($user_id)
	{
		$repo = $this->store()->getRepository('user_group_member');
		$repo->appendLockTables(['tbl_user']);
		$filter = [
			UserGroupMemberEntity::C_USER_ID => $user_id,
		];
		$entities = $this->finder()->filter($filter)->all();
		return $this->store()->deleteAll($repo,$entities);
	}
	/**
	 * Delete all members by group id
	 *
	 * @param integer $group_id
	 * @return boolean
	 */
	public function deleteAllByGroupId($group_id)
	{
		$repo = $this->store()->getRepository('user_group_member');
		$repo->appendLockTables(['tbl_user_group']);
		$filter = [
			UserGroupMemberEntity::C_GROUP_ID => $group_id,
		];
		$entities = $this->finder()->filter($filter)->all();
		return $this->store()->deleteAll($repo,$entities);
	}
	/**
	 * Finder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function finder()
	{
		return $this->store()->getFinder();
	}
}