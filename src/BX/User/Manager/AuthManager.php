<?php namespace BX\User\Manager;
use BX\Manager;
use BX\User\Entity\AuthEntity;
use BX\User\Entity\UserEntity;
use BX\User\Store\SessionAuthStore;

class AuthManager extends Manager
{
	use \BX\Http\HttpTrait,
	 \BX\Date\DateTrait;
	const COOKIE_KEY = 'ACCESS_TOKEN';
	const SECCOND_IN_HOUR = 3600;
	const DEFAULT_EXPIRE = 8460;
	private $unique_id = null;
	private $access_token = null;
	private $store = null;
	/**
	 * Get auth store
	 * @return \BX\User\Store\AuthStore
	 */
	private function store()
	{
		if ($this->store === null){
			$this->store = new SessionAuthStore();
		}
		return $this->store;
	}
	/**
	 * Get current auth
	 * @return boolean|AuthEntity
	 */
	private function get()
	{
		if ($this->string()->length($this->unique_id) === 0){
			return false;
		}
		if ($this->string()->length($this->access_token) === 0){
			return false;
		}
		$filter = [
			'UNIQUE_ID'		 => $this->unique_id,
			'ACCESS_TOKEN'	 => $this->access_token,
		];
		$sort = [
			'TIMESTAMP_X' => 'desc',
		];
		$return = false;
		$authes = AuthEntity::filter()->sort($sort)->filter($filter)->all();
		foreach($authes as $auth){
			if ($auth->expire > 0){
				$timestamp = $this->date()->makeTimeStamp($auth->timestamp_x);
				if ($timestamp + self::SECCOND_IN_HOUR * $auth->expire < time()){
					$auth->delete();
					break;
				}
			}
			if ($return !== false){
				$auth->delete();
			} else{
				$return = $auth;
			}
		}
		return $return;
	}
	/**
	 * Save auth
	 * @param \BX\User\Entity\UserEntity $user
	 * @param integer $expire
	 * @return type
	 */
	private function sucess(UserEntity $user,$expire = 0)
	{
		if ($expire <= 0){
			$expire = self::DEFAULT_EXPIRE;
		}
		$expire *= self::SECCOND_IN_HOUR;
		$this->request()->cookie()->set(self::COOKIE_KEY,$this->access_token,$expire);
		return $this->store()->save($user->id,$user->login,$user->email);
	}
	/**
	 * Init
	 */
	public function init()
	{
		if ($this->unique_id === null){
			$this->unique_id = $this->request()->server()->get('REMOTE_ADDR');
		}
		if ($this->access_token === null){
			$this->access_token = $this->request()->cookie()->get(self::COOKIE_KEY);
		}
	}
	/**
	 * Save auth for user
	 * @param \BX\User\Entity\UserEntity $user
	 * @param integer $expire
	 * @return type
	 */
	public function save(UserEntity $user,$expire = 0)
	{
		$auth = $this->get();
		if ($auth !== false){
			if (!$auth->update()){
				$mess = $this->trans('user.manager.auth.error_update');
				$this->log()->warning($mess);
			}
		} else{
			if ($this->string()->length($this->unique_id) > 0){
				$auth = AuthEntity::getEntity();
				$auth->unique_id = $this->unique_id;
				$auth->access_token = $this->access_token;
				$auth->user_id = $user->id;
				$auth->expire = $expire;
				if (!$auth->add()){
					$mess = $this->trans('user.manager.auth.error_update');
					$this->log()->warning($mess);
				}
			}
		}
		return $this->sucess($user,$expire);
	}
	/**
	 * Enter user
	 * @return boolean
	 */
	public function enter()
	{
		if ($this->store()->exits()){
			return $this->store();
		}
		$auth = $this->get();
		if ($auth !== false){
			if (!$auth->update()){
				$mess = $this->trans('user.manager.auth.error_update');
				$this->log()->warning($mess);
			}
			$user = UserEntity::filter()->filter(['ID' => $auth->user_id])->get();
			if ($user !== false){
				return $this->sucess($user,$auth->expire);
			} else{
				$mess = $this->trans('user.manager.auth.user_is_not_found',[
					'#USER_ID#' => $auth->user_id,
				]);
				$this->log()->warning($mess);
			}
		}
		return false;
	}
	/**
	 * Logout user
	 */
	public function logout()
	{
		$auth = $this->get();
		if ($auth !== false){
			$auth->delete();
		}
		$this->request()->cookie()->set(self::COOKIE_KEY,null,-1);
		$this->store()->delete();
		return true;
	}
}