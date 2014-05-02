<?php namespace BX\User;
use BX\Base\Registry;
use BX\User\Store\TableUserGroupMemberStore;

class UserGroupMemberManager
{
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
			if (Registry::exists('user','group_member','store')){
				$store = Registry::get('user','group_member','store');
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
		return $this->store()->add($user_id,$group_id);
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
		return $this->store()->delete($user_id,$group_id);
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