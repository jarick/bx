<?php namespace BX\User\Store;
use BX\Base;

abstract class AuthStore extends Base implements \BX\User\Store\IAuthStore
{
	/**
	 * @var integer
	 */
	public $id;
	/**
	 * @var string
	 */
	public $login;
	/**
	 * @var string
	 */
	public $email;
	/**
	 * @var string
	 */
	public $unique_id;
	abstract protected function get();
	abstract protected function set($sess);
	abstract protected function getUniqueId();
	/**
	 * Is exists save auth
	 * @return boolean
	 */
	public function exits()
	{
		$sess = $this->get();
		if ($sess !== null){
			$this->id = $sess[self::ID];
			$this->login = $sess[self::LOGIN];
			$this->email = $sess[self::EMAIL];
			$this->unique_id = $this->getUniqueId();
			return true;
		} else{
			return false;
		}
	}
	/**
	 * Save auth
	 * @param integer $id
	 * @param string $login
	 * @param string $email
	 * @return \BX\User\Store\SessionAuthStore
	 */
	public function save($id,$login,$email)
	{
		$this->id = $id;
		$this->login = $login;
		$this->email = $email;
		$this->unique_id = $this->getUniqueId();
		$sess = [
			self::ID	 => $this->id,
			self::LOGIN	 => $this->login,
			self::EMAIL	 => $this->email,
		];
		return $this->set($sess);
	}
	/**
	 * Remove auth
	 * @return \BX\User\Store\AuthStore
	 */
	public function delete()
	{
		$this->set(null);
		return $this;
	}
}