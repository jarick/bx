<?php namespace BX\User;
use BX\User\Entity\AccessEntity;
use BX\User\Store\IAccessStore;
use BX\User\Store\TableRememberPasswordStore;

class RememberPasswordManager
{
	use \BX\String\StringTrait,
	 \BX\Config\ConfigTrait;
	protected $store;
	/**
	 * @return IAccessStore
	 */
	protected function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('user','remember_password','store')){
				$store = $this->config()->get('user','remember_password','store');
				switch ($store){
					case 'db': $this->store = new TableRememberPasswordStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableRememberPasswordStore();
			}
		}
		return $this->store;
	}
	/**
	 * Check token
	 *
	 * @param string $guid
	 * @param string $token
	 * @return false|integer
	 */
	public function check($guid,$token)
	{
		$entity = $this->store()->get($guid,$token);
		if ($entity === false){
			return false;
		}
		return $entity->user_id;
	}
	/**
	 * Get token
	 *
	 * @param integer $user_id
	 * @param string $guid
	 * @param string $token
	 * @return string
	 */
	public function getToken($user_id,$guid,$token = null)
	{
		if ($token === null){
			$token = $this->string()->getRandString(8);
		}
		$entity = $this->store()->get($guid,$token);
		if ($entity === false){
			$this->clear($user_id);
			$entity = new AccessEntity();
			$entity->user_id = $user_id;
			$entity->guid = $guid;
			$entity->token = $token;
			$this->store()->add($entity);
		}
		return $token;
	}
	/**
	 * Clear tokens for user
	 *
	 * @param integer $user_id
	 * @return boolean
	 */
	public function clear($user_id)
	{
		return $this->store()->clear($user_id);
	}
	/**
	 * Clear old tokens
	 *
	 * @param integer $day
	 * @return boolean
	 */
	public function clearOld($day = 3)
	{
		return $this->store()->clearOld($day);
	}
}