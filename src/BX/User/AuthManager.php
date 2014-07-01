<?php namespace BX\User;
use BX\User\Entity\AccessEntity;
use BX\User\Store\IAccessStore;
use BX\User\Store\TableAuthStore;

class AuthManager
{
	use \BX\String\StringTrait,
	 \BX\Http\HttpTrait,
	 \BX\Config\ConfigTrait;
	const KEY = 'BX_AUTH';
	const COOKIE_GUID = 'BX_USER_GUID';
	const COOKIE_TOKEN = 'BX_USER_TOKEN';
	/**
	 * @var IAccessStore
	 */
	private $store = null;
	/**
	 * Get auth store
	 *
	 * @return IAccessStore
	 */
	private function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('user','auth','store')){
				$store = $this->config()->get('user','auth','store');
				switch ($store){
					case 'db': $this->store = new TableAuthStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableAuthStore();
			}
		}
		return $this->store;
	}
	/**
	 * Login user
	 *
	 * @param string $guid
	 * @param string $token
	 * @return boolean
	 */
	public function login($guid = null,$token = null)
	{
		if ($this->get() === null){
			if ($guid === null){
				$guid = $this->request()->cookie()->get(self::COOKIE_GUID);
			}
			if ($guid === null){
				return false;
			}
			if ($token === null){
				$token = $this->request()->cookie()->get(self::COOKIE_TOKEN);
			}
			if ($token === null){
				return false;
			}
			$access = $this->store()->get($guid,$token);
			if ($access === false){
				return false;
			}
			$data = $access->getData();
			unset($data[AccessEntity::C_TIMESTAMP_X]);
			unset($data[AccessEntity::C_TOKEN]);
			$this->session()->set(self::KEY,$data);
		}
		return true;
	}
	/**
	 * Logout
	 *
	 * @param string $guid
	 * @param string $token
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function logout($guid = null,$token = null)
	{
		if (!$this->login($guid,$token)){
			throw new \RuntimeException('Auth is not found');
		}
		$access = $this->get();
		if ($access === null){
			throw new \RuntimeException('Auth session is not found');
		}
		$this->session()->remove(self::KEY);
		return $this->store()->clear($access->user_id);
	}
	/**
	 * Clear old auth
	 *
	 * @param integer $day
	 * @return boolean
	 */
	public function clearOld($day = 30)
	{
		return $this->store()->clearOld($day);
	}
	/**
	 * Create new auth
	 *
	 * @param integer $user_id
	 * @param string $user_guid
	 * @param string $token
	 * @param boolean $http
	 * @return boolean
	 */
	public function add($user_id,$user_guid,$token = null,$http = true)
	{
		$this->store()->clear($user_id);
		if ($token === null){
			$token = $this->string()->getRandString(8);
		}
		$access = new AccessEntity();
		$access->guid = $user_guid;
		$access->user_id = $user_id;
		$access->token = $token;
		$this->store()->add($access);
		$this->session()->set(self::KEY,$access->getData());
		if ($http){
			$this->request()->cookie()->set(self::COOKIE_GUID,$access->guid);
			$this->request()->cookie()->set(self::COOKIE_TOKEN,$access->token);
		}
		return true;
	}
	/**
	 * Return auth from session
	 *
	 * @return AccessEntity|null
	 * @throws \RuntimeException
	 */
	public function get()
	{
		if ($this->session()->has(self::KEY)){
			$data = $this->session()->get(self::KEY);
			if ($data !== null){
				$access = new AccessEntity();
				$access->setData($data,true);
				return $access;
			}
		}
		return null;
	}
}