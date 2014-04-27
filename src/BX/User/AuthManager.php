<?php namespace BX\User;

class AuthManager
{
	private $auth_store = null;
	/**
	 * @return Store\IAccessStore
	 */
	private function getAccessStore()
	{
		// TODO: write logic here
	}
	public function check($guid,$token)
	{
		return $this->getAccessStore()->get($guid,$token);
	}
	public function add(BX\User\Entity\UserEntity $user,$save_in_cookie = true)
	{
		$token = $this->string()->getRandString(12);
		$user_id = $user->id;
		$guid = $user->guid;
		return $this->getAccessStore()->create($user_id,$guid,$token);
	}
	public function clear($user_id)
	{
		return $this->getAccessStore()->clear($user_id);
	}
	public function clearOld($day = 30)
	{
		return $this->getAccessStore()->clearOld($day);
	}
}