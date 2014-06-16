<?php namespace BX\User;
use BX\DB\Filter\SqlBuilder;
use BX\User\Store\TableUserStore;
use BX\User\Entity\UserEntity;
use BX\Event\Event;
use BX\Cache\Cache;

class UserManager
{
	use \BX\String\StringTrait,
	 \BX\Config\ConfigTrait,
	 \BX\Translate\TranslateTrait;
	private $store = null;
	/**
	 * @return TableUserStore
	 */
	private function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('user','store')){
				$store = $this->config()->get('user','store');
				switch ($store){
					case 'db': $this->store = new TableUserStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableUserStore();
			}
		}
		return $this->store;
	}
	/**
	 * Add user
	 *
	 * @param array $user
	 * @return boolean
	 */
	public function add(array $user)
	{
		$entity = new UserEntity();
		$entity->setData($user);
		$repo = $this->store()->getRepository('user');
		$filter = array(
			'LOGIC'				 => 'OR',
			UserEntity::C_LOGIN	 => $user[UserEntity::C_LOGIN],
			UserEntity::C_EMAIL	 => $user[UserEntity::C_EMAIL],
		);
		$copy = $this->finder()->filter($filter)->get();
		if ($copy !== false){
			$repo->rollback();
			if ($copy->getValue(UserEntity::C_LOGIN) === $user[UserEntity::C_LOGIN]){
				throw new \RuntimeException('Dublicate login');
			}
			if ($copy->getValue(UserEntity::C_EMAIL) === $user[UserEntity::C_EMAIL]){
				throw new \RuntimeException('Dublicate email');
			}
			throw new \RuntimeException('Dublicate login or email');
		}
		$event = $this->store()->getEvent();
		if ($this->string()->length($event) > 0){
			$event_after = 'OnAfter'.$this->string()->ucwords($event).'Add';
			Event::on($event_after,function(){
				Cache::clearByTags('user');
			});
		}
		return $this->store()->add($repo,$entity);
	}
	/**
	 * Update user
	 *
	 * @param integer $id
	 * @param array $user
	 * @return boolean
	 */
	public function update($id,array $user)
	{
		$repo = $this->store()->getRepository('user');
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("Error user is not found.");
		}
		$entity->setData($user);
		$filter = array(
			array(
				'LOGIC'				 => 'OR',
				UserEntity::C_LOGIN	 => $entity->login,
				UserEntity::C_EMAIL	 => $entity->email,
			),
			'!ID' => $id,
		);
		$copy = $this->finder()->filter($filter)->get();
		if ($copy !== false){
			$repo->rollback();
			if ($copy->getValue(UserEntity::C_LOGIN) === $entity->login){
				throw new \RuntimeException('Dublicate login');
			}
			if ($copy->getValue(UserEntity::C_EMAIL) === $entity->email){
				throw new \RuntimeException('Dublicate email');
			}
			throw new \RuntimeException('Dublicate login or email');
		}
		$event = $this->store()->getEvent();
		if ($this->string()->length($event) > 0){
			$event_after = 'OnAfter'.$this->string()->ucwords($event).'Update';
			Event::on($event_after,function(){
				Cache::clearByTags('user');
			});
		}
		return $this->store()->update($repo,$entity);
	}
	/**
	 * Delete user
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$repo = $this->store()->getRepository('user');
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("User is not found.");
		}
		$event = $this->store()->getEvent();
		if ($this->string()->length($event) > 0){
			$event_post = 'OnPost'.$this->string()->ucwords($event).'Delete';
			Event::on($event_post,function($id){
				if (!UserGroupMember::deleteAllByUserId($id)){
					throw new \RuntimeException("Error delete groups member.");
				}
			});
			$event_after = 'OnAfter'.$this->string()->ucwords($event).'Delete';
			Event::on($event_after,function(){
				Cache::clearByTags('user');
			});
		}
		return $this->store()->delete($repo,$entity);
	}
	/**
	 * Return filter
	 *
	 * @return SqlBuilder
	 */
	public function finder()
	{
		return $this->store()->getFinder();
	}
	/**
	 * Return password hash by user ID
	 *
	 * @param integer $user_id
	 * @return string
	 */
	public function getHashPasswordByUserID($user_id)
	{
		return $this->store()->getPasswordByUserID($user_id);
	}
	/**
	 * Return min length password
	 *
	 * @return integer
	 */
	protected function getMinLengthPassword()
	{
		if ($this->config()->exists('user','password_min_length')){
			return $this->config()->get('user','password_min_length');
		}
		return 6;
	}
	/**
	 * Hashing password
	 *
	 * @param string $value
	 * @return string
	 * @throw \InvalidArgumentException
	 */
	public function getHashPassword($value,$sold = 'sold')
	{
		if ($this->string()->length($value) === 0){
			return false;
		}
		$min = $this->getMinLengthPassword();
		if ($this->string()->length($value) < $min){
			return false;
		}
		return password_hash($value.md5($sold),PASSWORD_BCRYPT);
	}
	/**
	 * Return pasword verify
	 *
	 * @param string $value
	 * @param string $hash
	 * @param string $sold
	 * @return boolean
	 */
	public function checkPasswordByHash($value,$hash,$sold = 'sold')
	{
		return password_verify($value.md5($sold),$hash);
	}
	/**
	 * Update password
	 *
	 * @param integer $user_id
	 * @param string $password
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	public function updatePasssword($user_id,$password)
	{
		$repo = $this->store()->getRepository('user');
		$entity = $this->finder()->filter(['ID' => $user_id])->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("User is not found.");
		}
		$hash = $this->getHashPassword($password);
		if ($hash === false){
			$repo->rollback();
			throw new \InvalidArgumentException("Set bad password.");
		}
		return $this->store()->updatePassword($repo,$entity,$hash);
	}
}