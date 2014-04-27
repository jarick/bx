<?php namespace BX\User;

class UserManager
{
	use \BX\String\StringTrait;
	private $user_store = null;
	/**
	 * @return Store\TableUserStore
	 */
	private function getUserStore()
	{
		// TODO: write logic here
	}
	public function save(Entity\UserEntity $user)
	{
		return $this->getUserStore()->save($user);
	}
	public function filter()
	{
		return $this->getUserStore()->filter();
	}
	public function delete($user_id)
	{
		return $this->getUserStore()->delete($user_id);
	}
	public function getGroups()
	{

	}
}